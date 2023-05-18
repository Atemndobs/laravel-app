<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Song;
use App\Services\ClassifyService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class ClassificationController
 */
class ClassificationController extends Controller
{
    public Request $request;

    protected ClassifyService $classifyService;

    protected Song $song;

    /**
     * @param  ClassifyService  $classifyService
     * @param  Song  $song
     */
    public function __construct(
        ClassifyService $classifyService,
        Song $song,
        Request $request
    ) {
        $this->classifyService = $classifyService;
        $this->song = $song;
        $this->request = $request;
    }

    /**
     * @return array|string[]
     */
    public function classify()
    {
        return [];
    }

    /**
     * @return array|string[]
     */
    public function analyze(): array
    {
        $track = $this->request->track;
        try {
            $response = $this->classifyService->analyzeTrack($track);
        } catch (\Exception $e) {
            $response = [
                'error' => $e->getMessage(),
            ];
        }

        return $response;
    }

    /**
     * @param  Request  $request
     * @return Response
     */
    public function findByTitle(Request $request): Response
    {
        $slug = $request->slug;
        $song = Song::where('slug', '=', $slug)->first();

        if ($song) {
            $msg = [
                'id' => $song->id,
                'path' => $song->path,
            ];

            return \response($msg, 200);
        }

        $msg = [
            'SONG_NOT_FOUND' => "$slug does not exist , Please upload and try again",
        ];

        return \response($msg, 404);
    }
}
