<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Song\SearchSong;

class MeilesearchSongController extends Controller
{
    public SearchSong $search;

    public function __construct()
    {
        $this->search = new SearchSong();
    }

    public function getSongs()
    {
        return response()->json($this->search->getSongs());
    }

    public function getCleanedUpSongs()
    {
        return response()->json($this->search->getSongs());
    }

    public function ping()
    {
        $request = request()->all();
        info(json_encode($request));

        try {
        $status = $request['status'];
        if ($status == 'deleted') {
            return response()->json([
                'status' => 'delete notified',
            ]);
        }
        } catch (\Exception $e) {
            throw new \Exception('Process Deleted');
        }
        return response()->json([
            'status' => 'success',
        ]);
    }
}
