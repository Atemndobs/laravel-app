<?php

namespace App\Console\Commands\Recommendation;


use App\Models\Song;
use App\Services\Song\SongRecommendationService;
use Illuminate\Console\Command;

class getRecommendationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rec:get {--i|id=} {--d|dry-run} {--k|k-nearest=} {--l|limit=} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Song recommendation using Spotify Recommendation Model (nearest neighbor) based on k-nearest neighbors, song Id and limit
    e.g php artisan rec:get --id=98 --k-nearest=10 limit 3';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $songId = $this->option('id');
        $dryRun = $this->option('dry-run');
        $k = $this->option('k-nearest');
        $limit = $this->option('limit');

        $song = Song::query()->find($songId);
        if ($song === null) {
            $message =  [
                'error' => 'Song with id: ' . $songId . ' not found',
            ];
            $this->line("</fg=red>". json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ."</>");
        }
        $foundSong = [
            'endpoint' => '/api/v1/songs/search/' . $songId . '?k=' . $k,
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

        $this->info(json_encode($foundSong, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $recommendationService = new SongRecommendationService();
        $recommendation = $recommendationService->getNearestNeighbor($songId, $k);
        // dump($recommendation);
        $result  = [

            'songs' => $recommendation['songs'],
            'distances' => $recommendation['distances'],
            'ids' => $recommendation['ids'],
        ];
        $result = json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $this->line("</fg=green>Recommendation for song with id: $songId</>");
        $this->line("</fg=magenta>". $result ."</>");
        // put songs in a table
        $this->table([
            'id',
            'title',
            //'author',
            'key',
            'scale',
            'bpm',
            'path',
            'distance'
        ], $recommendation['songs']);

        //put distances in a table
       // $this->table(['distance'], $recommendation['distances']);
        return 0;
    }
}
