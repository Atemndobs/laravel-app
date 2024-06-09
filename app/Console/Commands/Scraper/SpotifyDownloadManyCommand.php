<?php

namespace App\Console\Commands\Scraper;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SpotifyDownloadManyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spotify-dl {--f|file=} {--d|dir=} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download Playlists from Spotify and maybe Youtube too. 
    Options : --force to force download even if song exists in DB. 
    --dir to specify download directory.
    --file to specify a file containing download links.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dir = $this->option('dir');
        $file = $this->option('file');
        $info = [
            'dir' => $dir,
            'file' => $file
        ];
        $this->info(json_encode($info, JSON_PRETTY_PRINT));

        // file is a txt containing download links. for each lin in the file, call the spotify command and pass link as url
        if ($file) {
            $file = file_get_contents($file);
            $file = explode("\n", $file);
            $file = array_filter($file);
            $file = array_unique($file);
            $fileCount = count($file);
            $this->info("Found $fileCount songs to download");
            $bar = $this->output->createProgressBar($fileCount);
            $bar->start();
            $processed = [];
            foreach ($file as $url) {
                try {
                    $this->call('spotify', [
                        'url' => $url,
                        '--force' => true,
                    ]);
                    $processed[] = $url;
                } catch (\Exception $e) {
                    $this->line("<fg=bright-magenta>Failed to download song with url $url</>");
                    $this->line("<fg=red>". $e->getMessage() ."</>");
                }
                $bar->advance();
                $this->line('');
            }
            $bar->finish();
            $this->info("\n");
            $this->info("Processed $fileCount songs");
            $this->info("Processed songs : " . implode("\n", $processed));
        } else {
            $this->error('No file specified');
        }
        return 0;
    }
}
