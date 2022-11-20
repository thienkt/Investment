<?php

namespace App\Http\Controllers;

use App\Events\SendPersonalNotification;
use App\Http\Requests\CreateTransactionRequest;
use App\Http\Requests\WithdrawRequest;
use App\Http\Resources\TransactionCollection;
use App\Models\FundTransaction;
use App\Models\Notification;
use App\Models\Transaction;
use App\Models\UserAsset;
use App\Models\UserPackage;
use App\Services\BankService;
use App\Services\TransactionService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    protected $bank;
    protected $transaction;

    public function __construct(BankService $bank, TransactionService $transaction)
    {
        $this->bank = $bank;
        $this->transaction = $transaction;
    }

    public function index()
    {
        $transactions = Transaction::where('purchaser', '=', Auth::id())->get();

        return $this->bank->ok(new TransactionCollection($transactions));
    }

    public function show($id)
    {
        $transaction = Transaction::find($id);

        $transactions = FundTransaction::where('transaction_id', '=', $id)->get();

        $transaction->detail = $transactions;

        return $this->bank->ok($transaction);
    }

    public function checkPayment($transactionId)
    {
        return $this->bank->checkPayment($transactionId);
    }

    public function createTransaction($packageId, CreateTransactionRequest $request)
    {
        $ref = $this->transaction->create(Auth::id(), $packageId, $request->amount);

        if (!$ref) {
            return $this->package->error(new Exception('Create transaction failed'));
        }

        Cache::put('payment-check-needed', now()->addMinutes(1)->toString());

        $bankInfo = $this->bank->getBankInfo();

        $msg = 'Vui lòng thanh toán số tiền ' . number_format($request->amount) . ' để tiến hành mua CCQ. Giao dịch sẽ tự động hủy sau 15 phút nếu không thanh toán.';
        $related_url = '/transactions/' . $ref;


        Notification::create([
            'user_id' => Auth::id(),
            'message' => $msg,
            'related_url' => $related_url,
            'status' => Notification::STATUS_UNREAD
        ]);

        broadcast(new SendPersonalNotification(Auth::id(), $msg, $related_url));

        return $this->transaction->ok(
            array_merge(
                $bankInfo,
                [
                    'reference_number' => $ref,
                    'transfer_amount' => number_format($request->amount),
                    'qr' => "https://img.vietqr.io/image/{$bankInfo['bank_code']}-{$bankInfo['account_number']}-qr_only.jpg?amount={$request->amount}&addInfo={$ref}"
                ]
            )
        );
    }

    public function withdraw(WithdrawRequest $request)
    {
        try {
            DB::beginTransaction();

            $request->session()->remove('auth.password_confirmed_at');

            $userId = Auth::id();
            $userPackage = UserPackage::where([
                'user_id' => $userId,
                'package_id' => $request->id
            ])->firstOrFail();

            $isValid = $this->bank->getBankAccountInfo($request->bank_id, $request->bank_account_id, true);

            if ($isValid !== true) {
                return $isValid;
            }

            $balance = 0;

            $transaction = $this->transaction->create($userId, $request->id, $request->amount, BankService::TYPE_WITHDRAW);

            $transaction->bank_id = $request->bank_id;
            $transaction->bank_account_id = $request->bank_account_id;

            $transaction->save();

            $transactions = [];

            foreach ($userPackage->package->funds as $index => $fund) {
                $percentage = $fund->pivot->allocation_percentage;
                $amount = $request->amount * $percentage / 100;

                $userAsset = UserAsset::firstOrCreate([
                    'user_package_id' => $userPackage->id,
                    'fund_id' => $fund->id,
                ]);

                $bookingAmount = Transaction::where(
                    [
                        'status' => BankService::STATUS_NEW,
                        'type' => BankService::TYPE_WITHDRAW,
                        'purchaser' => $userId,
                        ['id', '<>', $transaction->id]
                    ]
                )->sum('amount');

                $balance += $userAsset->amount * $userAsset->fund->current_value;

                $newFundTransaction = [
                    'amount' => $amount,
                    'status' => BankService::STATUS_NEW,
                    'type' => BankService::TYPE_SELL,
                    'transaction_id' => $transaction->id,
                    'purchaser' => $userId
                ];

                try {
                    $userAsset->fundTransactions()->save(new FundTransaction([
                        'amount' => $amount,
                        'status' => BankService::STATUS_NEW,
                        'type' => BankService::TYPE_SELL,
                        'transaction_id' => $transaction->id,
                        'purchaser' => $userId
                    ]));

                    array_push($transactions, $newFundTransaction);
                } catch (\Throwable $th) {
                    Log::error($th);
                }
            }

            $packageName = $userPackage->package->name;
            $msg = "Đặt lệnh bán thành công $request->amount VNĐ tử gói $packageName";
            $related_url = '/transactions/';

            Notification::create([
                'user_id' => Auth::id(),
                'message' => $msg,
                'related_url' => $related_url,
                'status' => Notification::STATUS_UNREAD
            ]);

            if ($balance - $bookingAmount < $request->amount) {
                DB::rollBack();

                return $this->transaction->error(new Exception("Không được rút số tiền lớn hơn ".$balance - $bookingAmount." VNĐ"));
            } else {
                DB::commit();
                broadcast(new SendPersonalNotification(Auth::id(), $msg, $related_url));
                $transaction->transactions = $transactions;

                return $this->transaction->ok($transaction);
            }
        } catch (\Throwable $th) {
            return $this->transaction->error($th);
        }
    }
}
