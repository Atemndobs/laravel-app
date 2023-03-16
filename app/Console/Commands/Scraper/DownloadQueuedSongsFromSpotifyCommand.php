<?php

namespace App\Console\Commands\Scraper;

use App\Models\Feed;
use Illuminate\Console\Command;

class DownloadQueuedSongsFromSpotifyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spotify:download';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download Spotify tracks from web browser';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $feeds = Feed::all();
        /** @var Feed $feed */
        foreach ($feeds as $feed) {

            try {
                $url = $feed->title;
                shell_exec("spotdl  $url --output storage/app/public/audio/");
                $feed->delete();
            }catch (\Exception $e) {
                dump($e->getMessage());
            }
        }
    }
}
