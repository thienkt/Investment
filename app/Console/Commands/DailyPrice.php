<?php

namespace App\Console\Commands;

use App\Services\FundService;
use Illuminate\Console\Command;

class DailyPrice extends Command
{
    private $fund;

    public function __construct(FundService $fund)
    {
        parent::__construct();
        $this->fund = $fund;
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'price:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'The fund credential price is updated daily.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->fund->updateDailyPrice();
    }
}
