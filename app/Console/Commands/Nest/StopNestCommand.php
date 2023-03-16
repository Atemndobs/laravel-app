<?php

namespace App\Console\Commands\Nest;

use Illuminate\Console\Command;

class StopNestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nest:stop';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Stop All Nestjs processes';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $processes = shell_exec('ps -aef | grep "nest" | grep node');
        $systems = explode("\n", $processes);

        $candidates = [];
        foreach ($systems as $system) {
            if ($system == null) {
                continue;
            }
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

        $this->output->caution('Deleted processess  : '.implode(' | ', $candidates));
    }
}
