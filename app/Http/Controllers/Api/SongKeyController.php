<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SongKey;
use Illuminate\Http\Request;

class SongKeyController extends Controller
{
    public function index()
    {
        $songKeys = SongKey::all();

        return response()->json($songKeys);
    }
}
