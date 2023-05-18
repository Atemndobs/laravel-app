<?php

namespace App\Http\Controllers\Api;

use AllowDynamicProperties;
use App\Http\Controllers\Controller;
use App\Models\Song;
use App\Services\Song\SongUploadService;
use App\Services\UploadService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class SongController
 */
#[AllowDynamicProperties] class SongUploadController extends Controller
{
    public Request $request;

    public SongUploadService $songUploadService;

    /**
     * @param SongUploadService $songUploadService
     * @param Song $song
     * @param Request $request
     */
    public function __construct(
        SongUploadService $songUploadService,
        Song $song,
        Request $request,
    ) {
        $this->songUploadService = $songUploadService;
        $this->song = $song;
        $this->request = $request;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadSong(Request $request): JsonResponse
    {
        $data = $request->allFiles();
        $uploadedSongs =  $this->songUploadService->uploadSong($data);
        if (count($this->songUploadService->getUploadedFiles()) < 1 && count($this->songUploadService->getErrors()) > 0) {
            $response = [
                'message' => 'Error uploading songs',
                'data' => $this->songUploadService->getErrors()
            ];
            return new JsonResponse($response, 400);
        }

        if (count($this->songUploadService->getErrors()) < 1) {
            $uploadedSongs['errors'] = 'No Errors';
        }
        $response = [
            'message' => "{$uploadedSongs['uploadedFilesCount'] } Songs uploaded successfully",
            'data' => $uploadedSongs
        ];

        return new JsonResponse($response, 200);
    }
}
