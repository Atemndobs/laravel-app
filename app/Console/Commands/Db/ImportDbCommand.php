<?php

namespace App\Console\Commands\Db;

use Illuminate\Console\Command;

class ImportDbCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        shell_exec('php artisan db');

        return 0;
    }
}
