<?php

namespace App\Console\Commands\Nest;

use Illuminate\Console\Command;

class RestartNestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nest:restart';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restart Nestjs Processess';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $node = shell_exec('pgrep node');
        $processes = shell_exec('ps -aef | grep "nest" | grep node');

        $nodeProcesses = explode("\n", $node);
        $systems = explode("\n", $processes);

        $candidates = [];

        foreach ($systems as $system) {
            if ($system == null) {
                continue;
            }
            //  $this->output->info($system);
            $systemProcesses = explode(' ', $system);
            foreach ($systemProcesses as $ky => $str) {
                if ($str == null) {
                    unset($systemProcesses[$ky]);
                }
            }
            $systemProcesses = array_values($systemProcesses);
            $candidates[] = $systemProcesses[1];
        }

        $deleted = implode(' ', $candidates);
        shell_exec("kill  $deleted");
        shell_exec('cd ../nested && /usr/local/bin/npm run start');

        $this->output->caution('Deleted processess  : '.implode(' | ', $candidates));
    }
}
