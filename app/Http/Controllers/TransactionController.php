<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionCollection;
use App\Models\Transaction;
use App\Services\BankService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    protected $bank;

    public function __construct(BankService $bank)
    {
        $this->bank = $bank;
    }

    public function index()
    {
        $transactions = Transaction::all();

        return $this->bank->ok(new TransactionCollection($transactions));
    }

    public function checkPayment($transactionId)
    {
        return $this->bank->checkPayment($transactionId);
    }
}
