<?php

namespace App\Services;

use App\Models\FundTransaction;
use App\Models\User;
use App\Models\UserAsset;
use App\Models\UserPackage;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserService extends BaseService
{
    protected $store;

    public function __construct(FirebaseService $store)
    {
        $this->store = $store;
    }

    public function changeAvatar($image)
    {
        try {
            $filePath = Config('app.asset_url') . $this->store->upload($image);
            $user = User::find(Auth::id());
            $user->avatar = $filePath;
            $user->save();

            return $this->ok('Success');
        } catch (Exception $e) {
            return $this->error($e);
        }
    }

    public function getAssetInfo()
    {
        $userPackages = UserPackage::with(['package', 'transactions'])->where('user_id', '=', Auth::id())->get();

        $userPackageIds = [];

        try {
            $userAssets = [];

            $total = 0;


            foreach ($userPackages as $key => $userPackage) {
                array_push($userPackageIds, $userPackage->id);

                $investmentAmount = 0;

                foreach ($userPackage->transactions as $key => $transaction) {
                    $investmentAmount += $transaction->amount;
                }

                $userPackage->investment_amount = $investmentAmount;
                $userPackage->profit = -$investmentAmount;
                $userPackage->balance = 0;

                $total += $investmentAmount;

                $userAssets[$userPackage->id] = $userPackage;
            }

            $assets = UserAsset::with(['fund', 'fundTransactions'])->whereIn('user_package_id', $userPackageIds)->get();

            $balance = 0;

            foreach ($assets as $key => $asset) {
                $amount = $asset->amount * $asset->fund->current_value;

                $balance += $amount;

                $userAssets[$asset->user_package_id]->balance += $amount;
                $userAssets[$asset->user_package_id]->profit += $amount;
            }

            $trans = FundTransaction::with('userAsset')->where('purchaser', '=', Auth::id())->get();

            foreach ($trans as $key => $transaction) {
                if ($transaction->status === BankService::STATUS_NEW && $transaction->type === BankService::TYPE_BUY) {
                    $balance += $transaction->amount;
                    $userAssets[$transaction->userAsset->user_package_id]->balance += $transaction->amount;
                    $userAssets[$transaction->userAsset->user_package_id]->profit += $transaction->amount;
                }

                if ($transaction->status === BankService::STATUS_SOLD && $transaction->type === BankService::TYPE_SELL) {
                    $amount = $transaction->volume * $transaction->price;
                    $userAssets[$transaction->userAsset->user_package_id]->profit += $amount;
                }
            }

            return $this->ok([
                'total_invest' => $total,
                'balance' =>  $balance,
                'packages' => $userAssets
            ]);
        } catch (\Throwable $th) {
            dd($th);
        }
    }
}
