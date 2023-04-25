<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Song;
use App\Services\Birdy\BirdyMatchService;
use Illuminate\Http\Request;

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


    public function getSongMatch(Request $request)
    {
        $slug = $request->input('slug');
        $res = $this->birdyMatchService->getSongmatch($slug);
        return response($res);
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
