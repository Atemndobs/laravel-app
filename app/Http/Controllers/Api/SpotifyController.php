<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Birdy\SpotifyService;
use Illuminate\Http\Request;

class SpotifyController extends Controller
{
    public SpotifyService $spotifyService;

    /**
     * @param  SpotifyService  $spotifyService
     */
    public function __construct(SpotifyService $spotifyService)
    {
        $this->spotifyService = $spotifyService;
    }

    /**
     * @param  Request  $request
     * @return array|string[]
     */
    public function getArtistGenre(Request $request)
    {
        return $this->spotifyService->getArtistGenre($request->artist);
    }

    public function getSpotifySearch(string $query)
    {
         return $this->spotifyService->getSpotifySearch($query);
    }
}
