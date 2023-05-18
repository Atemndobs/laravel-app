<?php

namespace App\Console\Commands\Song;

use App\Models\Song;
use App\Services\SongUpdateService;
use App\Services\UploadService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SongUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:update {slug?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Batch Update Song Details after Upload or Import. {slug} is optional and will update only one song.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $songUpdateService = new SongUpdateService();
        if ($slug = $this->argument('slug')) {
            $song = Song::query()->where('slug', $slug)->get()->first();
            if (!$song) {
                $this->output->error("Song $slug not found");
                return 0;
            }

            if ( !$song->bpm || !$song->key || !$song->scale || !$song->energy || !$song->duration
            ) {
                $this->output->info("$song->slug is already updated");
                $updatedSong = $song;
            }else{
                $this->info("updating $song->slug");
                $updatedSong = $this->updateBpmAndKeyAndScaleAndEnergyAndDuration($song, $songUpdateService);
            }

            $data[] = [
                'id' => $updatedSong->id,
                'title' => $updatedSong->title,
                'status' => 'updated',
            ];
        $headers = [
            'id',
            'title',
            'status',
        ];

        $this->output->table($headers, $data);
        return 0;
        }
        $songs = Song::query()->where('bpm', null)
            ->orWhere('key', null)
            ->orWhere('scale', null)
            ->orWhere('energy', null)
            ->orWhere('duration', null)
            ->get();

        $count = $songs->count();
        if ( $count === 0) {
            $this->output->info('All songs have been updated');
            return 0;
        }
        $this->info("updating $count songs");
        /** @var Song $song */
        foreach ($songs as  $song) {
            $this->info("Updating $song->slug");
            $updatedSong = $this->updateBpmAndKeyAndScaleAndEnergyAndDuration($song, $songUpdateService);
            $data[] = [
                'id' => $updatedSong->id,
                'title' => $updatedSong->title,
                'status' => 'updated',
            ];
        }
        $headers = [
            'id',
            'title',
            'status',
        ];

        $this->output->table($headers, $data);

        $total = count($data);
        $this->output->info("Updated  $total songs");
        info('=========================================UPDATE SONGS==========================================');

        return 0;
    }

    /**
     * @param  string  $base_url
     * @param  Model|Song  $song
     * @param  Collection  $songs
     * @param  array  $updated
     * @return int
     */
    public function updateSong(string $base_url, Model|Song $song, Collection $songs, array $updated): int
    {
        Http::get($base_url.$song->id);

        $total = count($songs);
        $rest = $total - count($updated);
        $this->output->info("Updated  $song->title   | $rest songs left");

        return $total;
    }

    /**
     * @param $song
     * @param SongUpdateService $songUpdateService
     * @return Song
     */
    public function updateBpmAndKeyAndScaleAndEnergyAndDuration($song, SongUpdateService $songUpdateService): Song
    {
        if (!$song->bpm || !$song->key || !$song->scale || !$song->energy) {
            $this->info("Updating $song->slug");
            $song = $songUpdateService->updateSongProperties($song);
        }
        if ($song->duration === null) {
            $song = $songUpdateService->getSongDuration($song);
        }
        $song->save();
        Log::info("Updated $song->slug  |  $song->bpm  |  $song->key  |  $song->scale  |  $song->energy  |  $song->duration");
        $this->line("<fg=magenta>Updated $song->slug | BPM : $song->bpm | Key : $song->key </fg=magenta>");

        return $song;
    }
}
