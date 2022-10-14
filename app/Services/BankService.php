<?php

namespace App\Services;

use App\Models\Transaction;
use Exception;
use Illuminate\Support\Facades\Cache;

class BankService extends VendorService
{
    const STATUS_NEW = 0;
    const STATUS_PAID = 1;
    const STATUS_BOUGHT = 2;
    const STATUS_SOLD = 3;
    const STATUS_ADVANCED_MONEY = 4;
    const STATUS_WITHDRAWN = 5;

    /**
     * @override
     */
    public function getCredential($credentialId = 0)
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

                    // TODO: buy fund credential
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
}
