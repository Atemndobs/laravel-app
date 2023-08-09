<?php

namespace App\Console\Commands\Scraper;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SpotifyDownloadCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spotify {url}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download Playlists from Spotify and maybe Youtube too';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Downloading Playlists...');
        $url = $this->argument('url');

        $songDownloadLocation = "/var/www/html/storage/app/public/uploads/audio/";

        $shell = shell_exec("spotdl  $url --output $songDownloadLocation");
        $this->info($shell);
        Log::info($shell);
        try {
            $outputs = explode("\n", $shell);
            // search the word "found" from output
            $result = "";
            foreach ($outputs as $output) {
                if (str_contains($output, 'Downloaded')) {
                    $result = $output;
                    $this->info($result);
                }
            }
            $this->info("Download Completed ... | $result");
            Log::info("Download Completed ... | $result");

        }catch (\Exception $e) {
            $this->error($e->getMessage());
        }

        // save to database
        $this->info('Saving to Database...');
        $song = new \App\Models\Song();
        // get song local path
        $path = array_unique(explode("Downloaded", $result));
      //  dd($path);

        return 0;
    }
}
