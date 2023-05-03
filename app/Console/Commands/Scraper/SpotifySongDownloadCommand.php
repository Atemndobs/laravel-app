<?php

namespace App\Console\Commands\Scraper;

use App\Console\Commands\Recommendation\Catalog\createCatalogCommand;
use App\Models\Song;
use App\Services\Birdy\SpotifyService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class

SpotifySongDownloadCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spotify:song  {--t|title=} {--d|db=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download Spotify Song By Title either from a list of titles or from the database. options --title --db';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $service = new SpotifyService();
        $db = $this->option('db');
        $title = $this->option('title');

        if (!$db && !$title) {
            $title = $this->ask('Enter Song Title');
        }

        if ($title) {
            $song = Song::query()
                ->where('title', 'like', '%' . $title . '%')
                ->get()->first;
            if ($song === null) {
                $this->output->info('Song Already Exists in Database');
                return 0;
            }

            $service->getSongFromTitle($title);
            return 0;
        }



        $songs = Song::query()
            ->where('status', 'like', '%' . 'deleted'. '%')
            ->get();

        $bar = $this->output->createProgressBar(count($songs));
        $bar->start();
        $downloaded = [];
        /** @var Song $song */
        foreach ($songs as $song) {

            $bar->advance();
            try {
                $this->info('Download directly from spotify');
                Log::info('Download directly from spotify');
                $service->getSongFromTitle($title);
                $downloaded[] = $song->title;
                $downloaded[] = $song->slug;
            }catch (\Exception $e) {
                $this->error('Direct Spotify Dnload failed :' . $e->getMessage());
                $song->status = 'spotify-not-found';
                continue;
            }
//            $this->info("Download with Spotify Dnload command $song->slug");
//            Log::info("Download with Spotify Dnload command $song->slug");
//            // call spotify download command
//            $this->call('spotify', [
//                'url' => $url
//            ]);
//            $song->status = 'downloaded';
//            $song->save();
//            $this->line('Song ' . $song->title . ' |' . 'new status' . $song->status);
        }
        $bar->finish();

        // put downloaded songs to table
        $this->table(['title', 'url'], $downloaded);
        $this->info("Downloaded songs: " . count($downloaded));
        Log::info("Downloaded songs: " . count($downloaded));
        return 0;
    }
}
