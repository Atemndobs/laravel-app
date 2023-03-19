<?php

namespace App\Services;

use App\Jobs\ClassifySongJob;
use App\Models\Song;
use Illuminate\Support\Benchmark;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use function Psy\debug;

class MoodAnalysisService
{
    public const BASE_URL = 'http://host.docker.internal:3000';
    public const BASE_DOCKER = 'http://nginx';

    public function getAnalysis(string $slug): array
    {
        $base_url = env('APP_ENV') == 'local' ? self::BASE_DOCKER : env('BASE_URL');
        $nest_port = env('APP_ENV') == 'local' ? ':3000' : env('NEST_PORT');
        $nest_base_url = $base_url . ":$nest_port";
        Log::info(json_encode([
            'process' => 'MoodAnalysisService::getAnalysis',
            'args' => func_get_args(),
            'slug' => $slug,
            'nest_base_url' => $nest_base_url,
        ]));

        $existingSong = Song::query()->where('slug', '=', $slug)->first();

        if (!$existingSong) {
            return [
                'status' => "$slug does not exist",
            ];
        }

        /**
         * @var Song $existingSong
         */
        if ($existingSong->analyzed) {
            dump([
                'analyzed' => $existingSong->analyzed,
                'Existing' => $existingSong->status,
            ]);
            Log::info(json_encode([
                'analyzed' => $existingSong->analyzed,
                'Existing' => $existingSong->status,
            ]));
            $existingSong->searchable();

            return [
                'status' => $existingSong->status,
            ];
        }

        $nest_url = $nest_base_url . "/song/$slug";
        $notAnalyzedSongs = Song::query()->where('analyzed', '=', null)->count();
        Log::info("Not analyzed songs: $notAnalyzedSongs");

        $req = Http::get($nest_url);
        if ($req->json('status') == 'error') {
            dump([
                'status' => 'error',
                'message' => $req->json(),
                'nest_url' => $nest_url,
            ]);

            Log::error(json_encode([
                'status' => 'error',
                'message' => $req->json(),
                'nest_url' => $nest_url,
            ]));
            return [
                'status' => 'error',
                'message' => $req->json(),
            ];
        }
        info("Job in progress for $slug");
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
            if ($song->analyzed == 0 && $song->duration >= 600) {
                $song->status = 'skipped';
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
}
