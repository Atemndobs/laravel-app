<?php

use App\Websockets\SocketHandler\UpdateSongSocketHandler;
use BeyondCode\LaravelWebSockets\Facades\WebSocketsRouter;
use Illuminate\Support\Facades\Route;
use TCG\Voyager\Facades\Voyager;


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
Route::get('/mail', function () {
    \Illuminate\Support\Facades\Mail::to('info@acurator.com')->send(new \App\Mail\MusicImportedMail());
    return new App\Mail\MusicImportedMail();
});



WebSocketsRouter::webSocket('/socket/song', UpdateSongSocketHandler::class);