<?php

namespace App\Console\Commands\Recommendation\Spotify;


use App\Models\Song;
use App\Services\Recommendation\SpotifyRecommendationService;
use App\Services\Song\SearchSong;
use App\Services\Song\SongRecommendationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class getSpotifyRecommendationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spotify:rec {--i|id=} {--d|dry-run} {--b|bpm=} {--l|limit=} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Recommendation from Spotify based omg spotify song id
    --id=spotify song id
    --dry-run
    --k-nearest=number of nearest neighbors
    ';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $songId = $this->option('id');
        $dryRun = $this->option('dry-run');
        $bpm = $this->option('bpm');
        $limit = $this->option('limit') ?? 100;

        $song = Song::query()->find($songId);
        if ($song === null) {
            $message =  [
                'error' => 'Song with id: ' . $songId . ' not found',
            ];
            $this->line("</fg=red>". json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ."</>");
        }

        // endpoint = https://www.chosic.com/api/tools/recommendations?seed_tracks=5aIVCx5tnk0ntmdiinnYvw&limit=100
        $endpoint = "https://www.chosic.com/api/tools/recommendations?seed_tracks=$songId&limit=$limit";
        $foundSong = [
            'endpoint' => $endpoint,
            'id' => $song->id,
            'title' => $song->title,
            'author' => $song->author,
            'key' => $song->key,
            'scale' => $song->scale,
            'bpm' => $song->bpm,
            'energy' => $song->energy,
            'danceability' => $song->danceability,
            'happy' => $song->happy,
            'sad' => $song->sad,
        ];

        $this->line("<fg=yellow>" . json_encode($foundSong, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "</>");
        $recommendationService  = new SpotifyRecommendationService();
        $songs = $recommendationService->getChosicRecommendation($songId, $limit);
        // find resulting songs in database
        $songs = Song::query()->whereIn('song_id', array_column($songs, 'id'))->get('id');
        $songSearch = new SearchSong();

        $ids = array_column($songs->toArray(), 'id');
        $filterString = implode(" OR ", array_map(function($id) {
            return "id = $id";
        }, $ids));
        $searchParams = ['filter' => $filterString];
        $foundSongs = $songSearch->getSongs(0, count($ids), $searchParams);

        $count = count($songs);
        $message = [
            'songs' => $foundSongs,
            'total_songs' => $count,
            'limit' => $limit,
        ];

        Log::info(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        $this->line("<fg=green>" . json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "</>");
        // output table with found songs
        return 0;
    }
}
