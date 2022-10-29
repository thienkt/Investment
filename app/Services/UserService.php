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

            $balance = 0;

            $profit = 0;

            $started_at = false;

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
                $profit -= $investmentAmount;

                $userAssets[$userPackage->id] = $userPackage;
            }

            $assets = UserAsset::with(['fund', 'fundTransactions'])->whereIn('user_package_id', $userPackageIds)->get();


            foreach ($assets as $key => $asset) {
                $amount = $asset->amount * $asset->fund->current_value;

                $balance += $amount;

                $userAssets[$asset->user_package_id]->balance += $amount;
                $userAssets[$asset->user_package_id]->profit += $amount;
                $profit += $amount;
            }

            $trans = FundTransaction::with('userAsset')->where('purchaser', '=', Auth::id())->orderBy('created_at')->get();


            foreach ($trans as $key => $transaction) {
                if (!$started_at) {
                    $started_at = $transaction->created_at;
                }

                if ($transaction->status === BankService::STATUS_NEW && $transaction->type === BankService::TYPE_BUY) {
                    $balance += $transaction->amount;
                    $userAssets[$transaction->userAsset->user_package_id]->balance += $transaction->amount;
                    $userAssets[$transaction->userAsset->user_package_id]->profit += $transaction->amount;
                    $profit += $transaction->amount;
                }

                if ($transaction->status === BankService::STATUS_SOLD && $transaction->type === BankService::TYPE_SELL) {
                    $amount = $transaction->volume * $transaction->price;
                    $userAssets[$transaction->userAsset->user_package_id]->profit += $amount;
                    $profit += $amount;
                }
            }

            $packages = [];
            foreach ($userAssets as $asset) {
                array_push($packages, $asset);
            }

            return $this->ok([
                'total_invest' => $total,
                'started_at' => $started_at,
                'profit' => $profit,
                'balance' =>  $balance,
                'packages' =>  $packages
            ]);
        } catch (\Throwable $th) {
            dd($th);
        }
    }
}
