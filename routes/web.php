<?php

use App\Services\Storage\AwsS3Service;
use App\Websockets\SocketHandler\UpdateSongSocketHandler;
use BeyondCode\LaravelWebSockets\Facades\WebSocketsRouter;
use Illuminate\Support\Facades\Route;
use TCG\Voyager\Facades\Voyager;
use App\Http\Controllers\SpotifyAuthController;
use App\Http\Controllers\SpotifyController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/upload', function (AwsS3Service $s3Service) {
//    $filePath = public_path('storage/Kali_Uchis-Telepatia.mp3');
//    $bucket = env('AWS_BUCKET', 'curators3');
//    $key = 'music/Kali_Uchis-Telepatia.mp3';
//
//    return $s3Service->uploadFile($filePath, $bucket, $key);
//});


Route::get('/', function () {

    event(new \App\Events\NewSongEvent('test'));
    return view('welcome');
});

Route::get('/login', function () {
    return view('welcome');
});
Route::get('health', \App\Http\Controllers\Admin\HealthCheckController::class);


Route::group(['prefix' => 'voyager'], function () {
    Voyager::routes();
});

// Mailing Route
//Route::get('/mail', function () {
//    \Illuminate\Support\Facades\Mail::to('info@acurator.com')->send(new \App\Mail\MusicImportedMail());
//    return new App\Mail\MusicImportedMail();
//});

Route::get('/broadcast', function () {
    broadcast(new \App\Events\NewSongEvent('Test : From The Broad Cast Controller'));
});


WebSocketsRouter::webSocket('/socket/song', UpdateSongSocketHandler::class);


Route::get('/spotify/login', [SpotifyAuthController::class, 'login'])->name('spotify.login');
Route::get('/spotify/callback', [SpotifyAuthController::class, 'callback'])->name('spotify.callback');


Route::get('/spotify/logged-in', [SpotifyController::class, 'loggedIn'])->name('spotify.logged_in');
Route::get('/open-browser', [SpotifyController::class, 'openBrowser']);


