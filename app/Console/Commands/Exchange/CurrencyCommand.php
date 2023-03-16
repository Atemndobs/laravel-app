<?php

namespace App\Console\Commands\Exchange;

use App\Services\Exchange\ExchangeService;
use Illuminate\Console\Command;

class CurrencyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exchange {base} {target} {amount}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Currency Exchange Command';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $base = $this->argument('base');
        $target = $this->argument('target');
        $amount = $this->argument('amount');

        $this->info("Converting {$amount} from {$base} to  {$target}");

        $exchanger = new ExchangeService();
        $result = $exchanger->convert($base, $target, $amount);

        dd([
            'base' => $base,
            'target' => $target,
            'amount' => $amount,
            'result' => $result,
        ]);

    }
}
