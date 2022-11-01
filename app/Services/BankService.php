<?php

namespace App\Services;

use App\Models\FundTransaction;
use App\Models\Transaction;
use App\Models\UserAsset;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class BankService extends VendorService
{
    const STATUS_NEW = 0;
    const STATUS_PAID = 1;
    const STATUS_BOUGHT = 2;
    const STATUS_SOLD = 3;
    const STATUS_ADVANCED_MONEY = 4;
    const STATUS_WITHDRAWN = 5;
    const STATUS_FAILURE = 6;

    const TYPE_BUY = 0;
    const TYPE_SELL = 1;
    const TYPE_WITHDRAWN = 2;


    /**
     * @override
     */
    public function getCredential($credentialId = 0, $forceUpdate = false)
    {
        try {
            $credential = Cache::get('bank:::credential', false);

            if (!$credential) {
                $bankConfig = Config('bank');
                $options = [
                    'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
                    'body' => json_encode([
                        'username' => base64_decode($bankConfig['username']),
                        'password' => base64_decode($bankConfig['password']),
                    ])
                ];
                $response = $this->post($bankConfig['login_url'], $options);
                $credential = $response->access_token;
                Cache::put('bank:::credential', $credential, now()->addMinutes(5));
            }

            return $credential;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getBankInfo()
    {
        $bankConfig = Config('bank');
        return [
            'bank_code' => $bankConfig['code'],
            'bank_name' => $bankConfig['name'],
            'account_number' => $bankConfig['account_number'],
            'beneficiary_name' => $bankConfig['accountant_holder'],
        ];
    }

    public function getTransactionHistory($keyword = "")
    {
        try {
            $credential = $this->getCredential();
            $bankConfig = Config('bank');
            $options = [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $credential
                ],
                'body' => json_encode([
                    'accountNo' => $bankConfig['account_number'],
                    'currency' => "VND",
                    'fromDate' => date("Ymd", strtotime("-1 days")),
                    'keyword' => $keyword,
                    'toDate' => date("Ymd")
                ])
            ];

            $response = $this->post($bankConfig['get_history_url'], $options);

            return $response->transactionInfos;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function checkPayment($id)
    {
        try {
            $transaction = Transaction::findOrFail($id);

            if ($transaction->status) {
                return $this->ok([
                    'payment_status' => (bool) $transaction->status
                ]);
            }

            $bankHistory = $this->getTransactionHistory();

            $matches = array();

            foreach ($bankHistory as $key => $value) {
                preg_match("/[a-zA-Z0-9]{16}/", $value->description, $matches);
                $ref = $matches[0] ?? null;

                if ($ref && $ref === $id && $value->amount == $transaction->amount) {
                    $transaction->status = 1;
                    $transaction->save();

                    $this->buyFundCertificate($transaction);

                    break;
                }
            }

            return $this->ok([
                'payment_status' => (bool) $transaction->status
            ]);
        } catch (Exception $e) {
            return $this->ok([
                'payment_status' => false
            ]);
        }
    }

    public function buyFundCertificate($transaction)
    {
        $userPackage = $transaction->userPackage;

        foreach ($userPackage->package->funds as $index => $fund) {
            $vendorConfig = Config('vendor');
            $tradeUrl = $vendorConfig['trade_url'];
            $accountId = $vendorConfig['account_id'];
            $percentage = $fund->pivot->allocation_percentage;
            $amount = $transaction->amount * $percentage / 100;

            $options = [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $fund->credential->token
                ],
                'body' => json_encode([
                    "action" => "BUY",
                    "status" => "BOOKING",
                    "transactionValue" => $amount,
                    "productCode" => $fund->code,
                    "autoPurchase" => 1,
                    "type" => [
                        "value" => "NORMAL",
                        "period" => "ST"
                    ],
                    "matchedDate" => date("Y-m-d", strtotime("2 days")),
                    "periodicOrder" => false,
                    "purchaserId" => $accountId,
                    "accountId" => $accountId,
                    "referralCode" => null
                ])
            ];


            $userAsset = UserAsset::firstOrCreate([
                'user_package_id' => $userPackage->id,
                'fund_id' => $fund->id,
            ]);

            try {
                $tradeResponse = $this->post($tradeUrl, $options);
            } catch (\Throwable $th) {
                $userAsset->fundTransactions()->save(new FundTransaction([
                    'amount' => $amount,
                    'status' => self::STATUS_FAILURE,
                    'type' => self::TYPE_BUY,
                    'ref' => $tradeResponse->id,
                    'transaction_id' => $transaction->id,
                    'purchaser' => Auth::id()
                ]));

                return;
            }

            $userAsset->fundTransactions()->save(new FundTransaction([
                'amount' => $amount,
                'status' => self::STATUS_NEW,
                'type' => self::TYPE_BUY,
                'ref' => $tradeResponse->id,
                'transaction_id' => $transaction->id,
                'purchaser' => Auth::id()

            ]));
        }
    }

    public function cancelFundTransaction($orderList, $accessToken)
    {
        $vendorConfig = Config('vendor');
        $tradeCancelUrl = $vendorConfig['trade_cancel_url'];

        $options = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $accessToken
            ],
            'body' => json_encode([
                'orderList' => $orderList
            ])
        ];

        $this->put($tradeCancelUrl, $options);
    }
}
