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
        $limit = $this->request->limit ?? 10;
        $search = $this->birdyMatchService->getSongmatch($slug);

        Log::info(json_encode([
            'method' => 'MatchSongController@getSongsMatch',
            'position' => 'After Try Catch',
            'limit' => $limit,
            'RaW - response' => $search,
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
