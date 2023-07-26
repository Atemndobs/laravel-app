<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Song;
use App\Services\Birdy\BirdyMatchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MatchSongController extends Controller
{
    /**
     * @var Request
     */
    public Request $request;

    /**
     * @var Song
     */
    public Song $song;

    /**
     * @var BirdyMatchService
     */
    public BirdyMatchService $birdyMatchService;

    /**
     * @param  Request  $request
     * @param  Song  $song
     * @param  BirdyMatchService  $birdyMatchService
     */
    public function __construct(Request $request, Song $song, BirdyMatchService $birdyMatchService)
    {
        $this->request = $request;
        $this->song = $song;
        $this->birdyMatchService = $birdyMatchService;
    }


    public function getSongMatch()
    {
        $slug = $this->request->input('slug');
        $bpm = $this->request->input('bpm');
        $bpmMin = null;
        $bpmMax = null;
        $bpmRange = $this->request->input('bpmRange');
        $happy = $this->request->input('happy');
        $sad = $this->request->input('sad');
        $key = $this->request->input('key');
        $energy = $this->request->input('energy');
        $mood = $this->request->input('mood');
        $danceability = $this->request->input('danceability');
        // get pbm range if bpm has more than 1 value
        if (str_contains($bpmRange, '-')) {
            $bpmRange = explode('-', $bpmRange);
            $bpmMin = $bpmRange[0];
            $bpmMax = $bpmRange[count($bpmRange) - 1];
        }


        $range = $this->request->range ?? 1;
        $limit = $this->request->limit ?? 10;
        $search = $this->birdyMatchService->getSongMatch(
            $slug,
            $key,
            $mood,
            $bpm,
            $bpmMin,
            $bpmMax,
            $happy,
            $sad,
            $energy,
            $danceability,
            $range
        );

        Log::info(json_encode([
            'method' => 'MatchSongController@getSongsMatch',
            'position' => 'After Try Catch',
            'limit' => $limit,
            //'RaW - response' => $search,
        ]));
        $search['limit'] = (int)$limit;

        // limit results to $limit
        $search['hits'] = array_slice($search['hits'], 0, $limit);

        
        return response($search);
    }

    /**
     * @param $song
     * @return array|array[]|\MeiliSearch\Search\SearchResult|\mixed[][]|void|null
     */
    public function matchByAttribute($song)
    {
        return $this->birdyMatchService->getMatchByAttribute($song);
    }
}
