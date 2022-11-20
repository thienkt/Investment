<?php

namespace App\Http\Controllers;

use App\Services\BankService;

class BankController extends Controller
{
    protected $bank;

    public function __construct(BankService $bank)
    {
        $this->bank = $bank;
    }

    public function getBankAccountInfo($bankId, $accountId)
    {
        return $this->bank->getBankAccountInfo($bankId, $accountId);
    }
}
