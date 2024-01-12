<?php

namespace App\Services;

use App\Jobs\ClassifySongJob;
use App\Models\Song;
use Illuminate\Support\Benchmark;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use TCG\Voyager\Models\Setting;
use function Psy\debug;

class MoodAnalysisService
{
    public const BASE_URL = 'http://host.docker.internal:3000';
    public const BASE_DOCKER = 'http://nestjs_api_prod';
    public array $missingSongs = [];

    public function getMissingSongs(): array
    {
        return $this->missingSongs;
    }

    public function addMissingSongs(string $missingSong): void
    {
        $this->missingSongs[] = $missingSong;
    }

    public function getAnalysis(string $slug): array
    {
//        $base_url = env('APP_ENV') == 'local' ? env('NEST_DOCKER_URL') : env('NEST_URL');
//        $nest_port = env('APP_ENV') == 'local' ? '3000' : env('NEST_PORT');
        //$nest_base_url = env('APP_ENV') == 'local' ? $base_url . ":$nest_port" : $base_url;

        $nest_base_url = Setting::query()->where('key', '=', 'nest.base_url')
            ->where('group', '=', 'nest')
            ->first()->value;

        Log::info(json_encode([
            'process' => 'MoodAnalysisService::getAnalysis',
            'args' => func_get_args(),
            'slug' => $slug,
            'nest_base_url' => $nest_base_url,
        ], JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));

        if (empty($slug)) {
            Log::warning(json_encode([
                'message' => 'Slug is empty',
                'status' => 404,
            ], JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
            return [
                'status' => 'Slug is empty',
            ];
        }


        $existingSong = Song::query()->where('slug', '=', $slug)->first();
        // Check song from s3 storage
        $existOnStorage = $this->checkSongOnStorage($slug);
        if (!$existingSong && !$existOnStorage) {
            Log::warning(json_encode([
                'message' => "$slug does not exist",
                'status' => 404,
            ], JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
//            return [
//                'status' => "$slug does not exist",
//            ];

            throw new \Exception("$slug does not exist");
        }

        /**
         * @var Song $existingSong
         */
        if ($existingSong && $existingSong->analyzed == 1) {
            Log::info(json_encode([
                'message' => 'Song already analyzed',
                'analyzed' => $existingSong->analyzed,
                'Existing' => $existingSong->status,
            ], JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
            $existingSong->searchable();

            return [
                'status' => $existingSong->status,
            ];
        }
        // if duration longer tha 10 min skip
        $uploadService = new SongUpdateService();
        $songDuration = $uploadService->getSongDuration($existingSong);

        if ($songDuration->duration > 700){
            $existingSong->status = 'skipped - mixtape';
            $existingSong->save();
            Log::warning(json_encode([
                'message' => 'Song skipped - may be a mixtape',
                'duration' => $songDuration,
            ], JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
            return [
                'status' => "Song skipped - may be a mixtape, Duration :  $songDuration",
            ];
        }

        $nest_url = $nest_base_url . "/song/$slug";
        $notAnalyzedSongs = Song::query()->where('analyzed', '=', null)->count();
        Log::info("Not analyzed songs: $notAnalyzedSongs");
        $req = Http::get($nest_url);

        if ($req->json('status') == 'error') {
            Log::error(json_encode([
                'status' => 'error',
                'message' => $req->json(),
                'nest_url' => $nest_url,
            ], JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
            return [
                'status' => 'error',
                'message' => $req->json(),
            ];
        }
        Log::info("Job in progress for $slug");
        return [
            'status' => 'Job sent for analysis ,Hang on!!',
        ];
    }

    public function classifySongs(): array
    {
        $songs = Song::all();
        $unClassified = [];
        $skipped = [];
        /** @var Song $song */
        foreach ($songs as $song) {
            if ($song->analyzed == 0 && $song->duration >= 500) {
                $song->status = 'skip';
                $song->analyzed = false;
                $song->save();
                $skipped[] = [
                    'song' => $song->slug,
                    'duration' => $song->duration,
                    'status' => $song->status,
                    'analyzed' => $song->analyzed,
                ];
            } elseif ($song->analyzed == null) {
                $song->status = 'queued';
                $song->save();
                $slug = $song->slug;
                $unClassified[] = $slug;
                ClassifySongJob::dispatch($slug);
                info("$slug : has been queued");
            }
        }

        return $unClassified;
    }

    private function checkSongOnStorage(string $slug) : bool
    {
        // try catch block to avaoid error when song does not exist
        try {
            $exists = Storage::disk('s3')->exists("music/$slug.mp3");
            if ($exists) {
                return true;
            }
        } catch (\Exception $e) {
            Log::error(json_encode([
                'message' => 'Error while checking song on storage',
                'slug' => $slug,
                'error' => $e->getMessage(),
            ], JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
            $this->addMissingSongs($slug);
            return false;
        }
        return false;
    }
}
