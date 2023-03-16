<?php

namespace App\Console\Commands\Server;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ServerMonitorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ser:check {process?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Custom Server Process Checker';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Checking Server Processes...');
        $process = $this->argument('process');
        $res = Http::post("http://localhost:8899/api/ping", [
            'json' => [
                'process' => $process,
                'response' => $res
            ],
        ]);

        dd($process);
        return 0;
    }
}
