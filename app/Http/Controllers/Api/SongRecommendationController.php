<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Song;
use App\Services\Song\SongRecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SongRecommendationController extends Controller
{
    public function getRecommendation()
    {
        $songId = \request()->songId;
        $k = \request()->k;

        $song = Song::query()->find($songId);
        if ($song === null) {
            $message =  [
                'error' => 'Song with id: ' . $songId . ' not found',
            ];
            Log::error(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            return response()->json($message);
        }

        Log::info('Found song with id: ' . $songId);
        $foundSong = [
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
        Log::info(json_encode($foundSong, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $recommendationService = new SongRecommendationService();
        $recommendation = $recommendationService->getNearestNeighbor($songId, $k);
        return response()->json($recommendation);

    }
}
