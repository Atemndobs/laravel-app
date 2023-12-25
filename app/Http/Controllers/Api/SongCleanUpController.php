<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SongCleanUpController extends Controller
{

    public function getCleanedUpSongs()
    {
        $songCleanUp = new \App\Services\Song\SongCleanUp();
        $cleanedUpSongs = $songCleanUp->CleanupAllSongs();
        return response()->json($cleanedUpSongs);
    }
}
