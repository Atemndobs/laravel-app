<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SpotifyWebAPI\SpotifyWebAPI;

class SpotifyController extends Controller
{
    public function loggedIn()
    {
        $accessToken = session('spotify_access_token');

        $api = new SpotifyWebAPI();
        $api->setAccessToken($accessToken);

        // It's now possible to request data about the currently authenticated user
        $user = $api->me();

        // Getting Spotify catalog data is also possible
        $track = $api->getTrack('7EjyzZcbLxW7PaaLua9Ksb');

        // Pass the retrieved data to the view
        return view('spotify.logged_in', compact('user', 'track'));
    }

    public function openBrowser()
    {
    }
}
