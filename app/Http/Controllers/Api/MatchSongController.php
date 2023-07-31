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


    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getSongMatch()
    {
        $slug = $this->request->input('slug') ?? null;
        if (is_null($slug) ) {
            return response([
                'hits' => [],
                'hit_count' => 0,
                "excluded_count" => 0,
                "limit" =>  0,

            ], 400);
        }
        $bpm = $this->request->input('bpm') ?? 0.00;
        $bpmMin = 0.00;
        $bpmMax = 0.00;
        $bpmRange = $this->request->input('bpmRange') ?? 0.00;
        $happy = $this->request->input('happy') ?? 0.00;
        $sad = $this->request->input('sad') ?? 0.00;
        $key = $this->request->input('key') ?? null;
        $energy = $this->request->input('energy') ?? 0.00;
        $mood = $this->request->input('mood') ?? 'happy';
        $danceability = $this->request->input('danceability') ?? 0.00;
        $id = $this->request->input('id') ?? null;
        $options = $this->request->input('options') ?? [];
        if (!is_array($options)) {
            $options = [];
        }
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
            $range,
            $id,
            $options,
            $limit
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
}
