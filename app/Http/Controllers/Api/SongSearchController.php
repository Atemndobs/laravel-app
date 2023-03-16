<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Song;
use App\Services\Birdy\MeiliSearchService;
use Illuminate\Http\Request;

class SongSearchController extends Controller
{
    public Request $request;

    public Song $song;

    public MeiliSearchService $meiliSearchService;

    /**
     * @param  Request  $request
     * @param  Song  $song
     * @param  MeiliSearchService  $meiliSearchService
     */
    public function __construct(Request $request, Song $song, MeiliSearchService $meiliSearchService)
    {
        $this->request = $request;
        $this->song = $song;
        $this->meiliSearchService = $meiliSearchService;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function searchSong()
    {
        $response = Song::search($this->request->get('query'), [
            'filter' => "status != 'deleted'",
            'sort' => ['bpm:asc'],
        ]);

        return response($response);
    }
}
