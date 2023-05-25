<?php

namespace App\Console\Commands\Scraper;

use App\Services\Scraper\SoundcloudService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Statamic\Support\Str;

class SoundcloudDownloadCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:sc {link?} {--a|artist=null} {--p|playlist=null} {--t|title=null} {--m|mixtape=null}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download music from Soundcloud by Link, artist name tiles or playlist';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->argument('link') === 'null') {
            $this->info('Please provide a link');
            return 0;
        }
        $link = $this->argument('link');
        try {
            // pass output to dev/null to prevent command from hanging
            $shell = shell_exec("scdl  -l $link -c 2>&1");
            Log::info($shell);
            $dl = explode("\n", trim($shell));
            $dl = array_filter($dl, function ($line) {
                return str_contains($line, 'Downloading');
            });
            // remove "Downloading" from the line
            $dl = str_replace('Downloading', '', $dl);
            $dl = implode("\n", $dl);
            $dl = trim($dl);
            $dl = Str::slug($dl, '_');

            $this->info(json_encode(["slug" =>  $dl], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
            Log::info(json_encode(["slug" =>  $dl], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

            //$this->call('song:import');

        }catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        return 0;
    }
}
