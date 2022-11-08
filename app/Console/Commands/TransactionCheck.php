<?php

namespace App\Console\Commands;

use App\Services\TransactionService;
use Illuminate\Console\Command;

class TransactionCheck extends Command
{
    private $transaction;

    public function __construct(TransactionService $transaction)
    {
        parent::__construct();

        $this->transaction = $transaction;
    }
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaction:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check transaction status';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->transaction->check();
    }
}
