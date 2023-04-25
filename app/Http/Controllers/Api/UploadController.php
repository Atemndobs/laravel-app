<?php

namespace App\Http\Controllers\Api;

use AllowDynamicProperties;
use App\Http\Controllers\Controller;
use App\Models\Song;
use App\Services\UploadService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class SongController
 */
#[AllowDynamicProperties] class UploadController extends Controller
{
    public Request $request;

    public UploadService $uploadService;

    /**
     * @param  uploadService  $uploadService
     * @param  Song  $song
     * @param  Request  $request
     */
    public function __construct(
        UploadService $uploadService,
        Song $song,
        Request $request,
    ) {
        $this->uploadService = $uploadService;
        $this->song = $song;
        $this->request = $request;
    }

    /**
     * @param Request $request
     * @return Application|ResponseFactory|Response
     */
    public function upload(Request $request): Response|Application|ResponseFactory
    {
        $data = $request->path;
        return $this->uploadService->uploadSong($data);
    }
}
