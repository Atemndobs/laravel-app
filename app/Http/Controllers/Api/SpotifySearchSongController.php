<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Scraper\SpotifyMusicService;
use Illuminate\Http\Request;

class SpotifySearchSongController extends Controller
{
    public function searchSong(Request $request)
    {
      //  dd($request->all());
        $search_query = $request->get('search_query');
        // explode search query byx - and get the first element as title and the second element as artist
        $search_query = explode('-', $search_query);
        $title = $search_query[0];
        if (count($search_query) > 1) {
            $artist = $search_query[1];
        } else {
            $artist = '';
        }
        $spotifyService = new SpotifyMusicService();
        $searchResult = $spotifyService->searchSong($title, $artist);
        return response()->json($searchResult);
    }
}
