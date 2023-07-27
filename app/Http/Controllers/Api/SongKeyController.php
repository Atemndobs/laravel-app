<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SongKey;
use Illuminate\Http\Request;

class SongKeyController extends Controller
{
    public function index() : \Illuminate\Http\JsonResponse
    {
        $songKeys = SongKey::all();

        return response()->json($songKeys);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSongKeys() : \Illuminate\Http\JsonResponse
    {
        // retrive song keys from song_keys table and return them as jsoni the format "label" => "key_name", value => "key_id"
        $songKeys = SongKey::all();
        $songKeysArray = [];
        foreach ($songKeys as $songKey) {
            $songKeysArray[] = [
                'label' => $songKey->key_name,
                'value' => $songKey->id,
            ];
        }
        return response()->json($songKeysArray);
    }
}
