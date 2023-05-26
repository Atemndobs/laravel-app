<?php

namespace App\Jobs;

use App\Models\Song;
use App\Services\SongUpdateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SongUpdateBpmJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $track;

    /**
     * Create a new job instance.
     */
    public function __construct(string $track)
    {
        $this->queue = 'bpm';
        $this->track = $track;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Pushing : ' . $this->track . 'to queue' . $this->queue);
        $bpm = true;
        $key = true;
        $updateService = new SongUpdateService();
        $song = Song::query()->where('slug', $this->track)->first();
        $this->getUpdatedSong($bpm, $key, $updateService, $song);

    }


    /**
     * @param bool $bpm
     * @param bool $key
     * @param SongUpdateService $updateService
     * @param $song
     * @return array
     * @throws \Exception
     */
    public function getUpdatedSong(bool $bpm, bool $key, SongUpdateService $updateService, $song): array
    {
        if (!$bpm && !$key) {
            $song = $updateService->updateBpmAndKey($song);
        }
        if ($bpm && !$key) {
            $song = $updateService->updateBpm($song);
        }
        if ($key && !$bpm) {
            $song = $updateService->updateKey($song);
        }

        return $song;
    }
}
