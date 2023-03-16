<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\AnalyzeSongJob;
use App\Models\Song;
use App\Services\Strapi\StrapiSongService;
use App\Services\UploadService;
use Illuminate\Http\Request;

/**
 * Class SongController
 */
class UploadController extends Controller
{
    public Request $request;

    public UploadService $uploadService;

    public StrapiSongService $strapiSongService;

    /**
     * @param  uploadService  $uploadService
     * @param  Song  $song
     * @param  Request  $request
     * @param  StrapiSongService  $strapiSongService
     */
    public function __construct(
        UploadService $uploadService,
        Song $song,
        Request $request,
        StrapiSongService $strapiSongService
    ) {
        $this->uploadService = $uploadService;
        $this->song = $song;
        $this->request = $request;
        $this->strapiSongService = $strapiSongService;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function upload(Request $request)
    {
        $data = $request->path;
        if (is_array($data)) {
            return $this->uploadService->batchUpload($data);
        }

        return $this->uploadService->uploadSong($data);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getStrapiUploads(): array
    {
        return $this->strapiSongService->importStrapiUploads();
    }

    /**
     * @param  Request  $request
     * @return void
     */
    public function strapiUploadsWebhook(Request $request): void
    {
        $payload = $request->all();

        if ($payload['event'] === 'media.create') {
            $song = $payload['media'];
            $res = $this->strapiSongService->importStrapiSong($song);
            $title = $res[0]->title;
            info('Imported : '.$title);
            AnalyzeSongJob::dispatch($title)->onConnection('database')->onQueue('analyze');
        }

        if ($payload['event'] === 'media.update') {
            $song = $payload['media'];
            $res = $this->strapiSongService->importStrapiSong($song);

            info('Imported : '.$res[0]->path);
        }

        if ($payload['event'] === 'media.delete') {
            $track = $payload['media'];
            $song = Song::where('title', '=', $track['name'])->first();
            $song->delete();

            info('Deleted : '.$song->title);
        }
    }
}
