<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Cache;

class BankService extends VendorService
{
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

    public function checkPayment()
    {
        $this->getTransactionHistory();
    }
}
