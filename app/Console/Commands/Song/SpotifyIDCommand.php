<?php

namespace App\Console\Commands\Song;

use App\Services\Scraper\SpotifyMusicService;
use Illuminate\Console\Command;

class SpotifyIDCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:spotify-id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Spotify ID for songs that do not have one.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $songs = \App\Models\Song::query()->whereNull('song_id')
            ->get();
        $spotifyService = new SpotifyMusicService();

        // start progress bar
        $bar = $this->output->createProgressBar(count($songs));
        $songsUpdated = [];

        /**
         * @var \App\Models\Song $song
         */
        foreach ($songs as $song) {
            // if author or title is null skip
            if (is_null($song->title) || is_null($song->author)) {
                continue;
            }
            $this->warn('Searching for ' . $song->title . ' by ' . $song->author);
            $this->line('');

            $songId = $spotifyService->searchSongByTitleAndArtist($song->title, $song->author);

            if ($songId) {
                $song->song_id = $songId;
                $song->song_url = 'https://open.spotify.com/track/' . $songId;
                $song->source = 'spotify';
                $song->save();
                $songsUpdated[] = $songId;
                $this->info('Found song with ID ' . $songId . ' and saved it to the database.');
                $this->line('');
            }else{
                $this->error('Could not find song with title ' . $song->title . ' and artist ' . $song->author);
                $this->line('');
            }
            $bar->advance();
            $this->line('');
        }

        // finish progress bar
        $bar->finish();
        $message = [
            'message' => 'Finished searching for Spotify IDs. Total songs found: ' . count($songs) . '.',
            'songs found' => count($songs),
            'songs updated' => count($songsUpdated),
        ];
        $this->warn(json_encode($message, JSON_PRETTY_PRINT));
    }
}
