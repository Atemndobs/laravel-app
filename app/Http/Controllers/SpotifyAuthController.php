<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SpotifyWebAPI\Session;

class SpotifyAuthController extends Controller
{
    public function login()
    {
//        dd( env('SPOTIFY_CLIENT_ID'),
//            env('SPOTIFY_CLIENT_SECRET'),
//            env('SPOTIFY_REDIRECT_URI')
//        );
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
            ],
            'state' => $state,
        ];
        // Store the state in the session so that the callback can verify the state is the same
        session(['spotify_state' => $state]);
      //  header('Location: ' . $session->getAuthorizeUrl($options));
        return redirect()->to($session->getAuthorizeUrl($options));
    }

    public function callback(Request $request)
    {

        $session = new Session(
            env('SPOTIFY_CLIENT_ID'),
            env('SPOTIFY_CLIENT_SECRET'),
            env('SPOTIFY_REDIRECT_URI')
        );

        $state = $request->query('state');
        $storedState = session('spotify_state');

        if ($state !== $storedState) {
            // The state returned isn't the same as the one we've stored, we shouldn't continue
            die('State mismatch');
        }
        // Request an access token using the code from Spotify
        $session->requestAccessToken($request->query('code'));
        // Store the access and refresh tokens somewhere. In a session for example
        session([
            'spotify_access_token' => $session->getAccessToken(),
            'spotify_refresh_token' => $session->getRefreshToken(),
        ]);

        // Redirect the user to the desired route after successful authentication
        return redirect()->route('spotify.logged_in');
    }
}