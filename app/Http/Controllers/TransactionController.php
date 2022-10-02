<?php

namespace App\Http\Controllers;

use App\Services\BankService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    protected $bank;

    public function __construct(BankService $bank)
    {
        $this->bank = $bank;
    }

    public function checkPayment($transactionId)
    {
        $this->bank->checkPayment();
    }
}
