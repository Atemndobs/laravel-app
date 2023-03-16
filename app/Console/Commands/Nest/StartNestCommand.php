<?php

namespace App\Console\Commands\Nest;

use Illuminate\Console\Command;

class StartNestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nest:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start Nestjs Api Server';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        shell_exec('cd ../nested &&  /usr/local/bin/npm run start ');

        return 0;
    }
}
