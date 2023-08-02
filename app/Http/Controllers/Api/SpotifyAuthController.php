<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SpotifyWebAPI\Session;

class SpotifyAuthController extends Controller
{
    public function login()
    {
        // clear all sessions
        session()->flush();
        $session = new Session(
            env('b0ba238623c6499b9fab2d7f5c497d8f'),
            env('32863d8fb52440fc9d35a654f2cf0df1'),
            env('http://core.curator.atemkeng.eu/api/spotify/callback')
        );

         $session = new Session(
            env('SPOTIFY_CLIENT_ID'),
            env('SPOTIFY_CLIENT_SECRET'),
            env('SPOTIFY_REDIRECT_URI')
        );

        $state = $session->generateState();
        $options = [
            'scope' => [
                'playlist-read-private',
                'user-read-private',
                'playlist-modify-public',
                'playlist-modify-private',
            ],
            'state' => $state,
        ];

        session(['spotify_auth' => $session->getAuthorizeUrl($options)]);
        return redirect()->away($session->getAuthorizeUrl($options));
    }

    public function callback(Request $request)
    {
        $session = new Session(
            env('b0ba238623c6499b9fab2d7f5c497d8f'),
            env('32863d8fb52440fc9d35a654f2cf0df1'),
            env('http://core.curator.atemkeng.eu/api/spotify/callback')
        );

//        $session = new Session(
//            env('SPOTIFY_CLIENT_ID'),
//            env('SPOTIFY_CLIENT_SECRET'),
//            env('SPOTIFY_REDIRECT_URI')
//        );

        $state = $request->input('state');
        $storedState = session('spotify_state');
        $session->requestAccessToken($request->input('code'));

        $accessToken = $session->getAccessToken();
        $refreshToken = $session->getRefreshToken();
        $expires = $session->getTokenExpiration();
        // Store the access and refresh tokens somewhere (e.g., session or database)
        session(['spotify_access_token' => $accessToken]);
        session(['spotify_refresh_token' => $refreshToken]);
        $spotifyAuth = new \App\Models\SpotifyAuth();
        // delete all records from the table
        \App\Models\SpotifyAuth::truncate();
        $spotifyAuth->access_token = $accessToken;
        $spotifyAuth->refresh_token = $refreshToken;
        $spotifyAuth->expires = $expires;
        $spotifyAuth->save();

        return redirect('/api/spotify/app');
    }
}