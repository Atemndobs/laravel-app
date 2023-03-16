<?php

namespace App\Console\Commands\Nest;

use Illuminate\Console\Command;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Support\Facades\Http;

class WatchNestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nest:watch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restart Nest if it stops';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //  # * * * * * /usr/bin/php8.1 artisan nest:watch >> ~/sites/curator/laravel/storage/logs/helathCheck.log 2>&1
        try {
            $healthCheck = $this->healthCheck();
            if ($healthCheck->status() === 200) {
                $header = [
                    'status',
                    'time',
                ];

                $data = [
                    [
                        'status' => $healthCheck->status(),
                        'response' => now('CET'),
                    ],
                ];
                $this->output->table($header, $data);

                return;
            }
        } catch (HttpClientException $exception) {
            // $this->output->info($exception->getMessage());
            $this->reStartNest();
        }

        return 0;
    }

    public function healthCheck()
    {
        $url = 'http://127.0.0.1:3000/song';

        return Http::get($url);
    }

    public function reStartNest()
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
        // shell_exec("kill  $deleted");
        shell_exec('cd ../nested && /usr/local/bin/npm run start');

        $this->output->caution('Deleted processess  : '.implode(' | ', $candidates));
    }
}
