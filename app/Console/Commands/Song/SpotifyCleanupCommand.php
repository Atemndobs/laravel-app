<?php

namespace App\Console\Commands\Song;

use Illuminate\Console\Command;

class SpotifyCleanupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:spotify-clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add spotify url to songs with song_id and source spotify.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Delete all song_ids that are source = spotify
        $songs = \App\Models\Song::where('source', 'spotify')->get();
        info('Found ' . count($songs) . ' songs with source spotify.');
        // start progress bar
        $bar = $this->output->createProgressBar(count($songs));
        foreach ($songs as $song) {
            $song->song_id = null;
            $song->save();
            $bar->advance();
            $this->warn('Deleted song_id for song ' . $song->id . ' - ' . $song->title);
        }
        $bar->finish();
        $this->info('Deleted all song_ids for songs with source spotify.');
    }
}
