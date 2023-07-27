<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\Request;

class SongGenreController extends Controller
{
    public function index()
    {
        // retrieve song genres from genres table and return them as json
        $genres = Genre::all();
        return response()->json($genres);

    }

    public function getSongGenres()
    {
        // retrieve song genres from genres table and return them as json the format "label" => "genre_name", value => "genre_id"
        $genres = Genre::all();
        $genresArray = [];
        foreach ($genres as $genre) {
            $genresArray[] = [
                'label' => $genre->name,
                'value' => $genre->id,
            ];
        }
        return response()->json($genresArray);
    }

}
