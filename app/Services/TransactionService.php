<?php

namespace App\Services;

use App\Models\FundTransaction;
use App\Models\Transaction;
use App\Models\UserPackage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TransactionService extends BaseService
{
    public $vendor;

    public function __construct(VendorService $vendor)
    {
        $this->vendor = $vendor;
    }

    public function create($userId, $packageId, $amount)
    {
        try {
            $userId = Auth::user()->id;
            $userPackage = UserPackage::where([
                'user_id' => $userId,
                'package_id' => $packageId
            ])->firstOrFail();

            $time = Carbon::now()->timestamp;
            $transactionId = getRandomString(16, $time . $userId);

            $transaction = Transaction::create([
                'id' =>  $transactionId,
                'status' => 0,
                'amount' => $amount,
                'purchaser' => $userId
            ]);

            $userPackage->transactions()->save($transaction);

            return $transactionId;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function check()
    {
        try {
            $transactions = FundTransaction::where('status', '=', BankService::STATUS_NEW)->get();

            Log::info(($transactions));
        } catch (\Throwable $th) {
        }
    }
}
