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
                        'username' => $bankConfig['username'],
                        'password' => $bankConfig['password'],
                    ])
                ];
                $response = $this->post($bankConfig['login_url'], $options);
                $credential = $response->access_token;
                Cache::put('bank:::credential', $credential, now()->addMinutes(15));
            }

            return $credential;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
