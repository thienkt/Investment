<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionCollection;
use App\Models\FundTransaction;
use App\Models\Transaction;
use App\Services\BankService;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    protected $bank;

    public function __construct(BankService $bank)
    {
        $this->bank = $bank;
    }

    public function index()
    {
        $transactions = Transaction::where('purchaser', '=', Auth::id())->get();

        return $this->bank->ok(new TransactionCollection($transactions));
    }

    public function show($id)
    {
        $transaction = Transaction::find($id);

        $transactions = FundTransaction::where('transaction_id', '=', $id)->get();

        $transaction->detail = $transactions;

        return $this->bank->ok($transaction);
    }

    public function checkPayment($transactionId)
    {
        return $this->bank->checkPayment($transactionId);
    }
}
