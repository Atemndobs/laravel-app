<?php

namespace App\Services\Spotify;

use App\Http\Controllers\Api\SpotifyAppController;
use App\Http\Controllers\Api\SpotifyAuthController;
use App\Models\SpotifyAuth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use SpotifyWebAPI\SpotifyWebAPI;

class SpotifyAuthService
{
    public function auth()
    {
        $controller = app()->make(SpotifyAuthController::class);
        $controller->login();
        $authUrl = session('spotify_auth');

//        $spotify_auth = new SpotifyAuth();
//        $spotify_auth->auth_url = $authUrl;
//        $spotify_auth->save();
        $authUrl = json_encode($authUrl, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
       // Http::get('http://directus:8055/webhooks/spotify');

        // $command = "lynx $authUrl" ;  // Command to open URL with lynx and retrieve page content
        //$command = "python3 auth.py $authUrl" ;  // Command to open URL with lynx and retrieve page content
        $command = "python3 auth.py $authUrl" ;  // Command to open URL with lynx and retrieve page content
       // $output = shell_exec($command);  // Execute the command and store the output

        //dd($output);

        return $authUrl;
    }
}