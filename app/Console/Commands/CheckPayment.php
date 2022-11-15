<?php

namespace App\Console\Commands;

use App\Events\SendPersonalNotification;
use App\Models\Notification;
use App\Models\Transaction;
use App\Services\BankService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CheckPayment extends Command
{
    private $bank;

    public function __construct(BankService $bank)
    {
        parent::__construct();
        $this->bank = $bank;
    }
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check payment status.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $checkNeededTime = Cache::get('payment-check-needed');
        $now = now()->toString();
        $matches = array();

        $transactionList = [];
        $transactionData = [];

        if ($checkNeededTime === $now || date('i') % 15 === 0) {
            $bankHistory = $this->bank->getTransactionHistory();

            foreach ($bankHistory as $key => $tran) {
                preg_match("/[a-zA-Z0-9]{16}/", $tran->description, $matches);
                $ref = $matches[0] ?? null;

                if ($ref) {
                    array_push($transactionList, $ref);
                    $transactionData[$ref] = $tran->amount;
                }
            }

            $transactions = Transaction::whereIn('id', $transactionList)->where('status', '=', $this->bank::STATUS_NEW)->get();

            foreach ($transactions as $transaction) {

                $this->bank->buyFundCertificate($transaction);

                $transaction->status = BankService::STATUS_PAID;
                $transaction->save();

                Notification::create([
                    'user_id' => $transaction->purchaser,
                    'message' => $transaction->id . ': ' . 'Payment success',
                    'related_url' => '',
                    'status' => Notification::STATUS_UNREAD
                ]);

                broadcast(new SendPersonalNotification($transaction->purchaser, $transaction->id . ': ' . 'Payment success', ''));
            }
        }
    }
}
