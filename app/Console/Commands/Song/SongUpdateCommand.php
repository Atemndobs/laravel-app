<?php

namespace App\Console\Commands\Song;

use App\Models\Song;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class SongUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:update {id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Batch Update Song Details From AnalyseAPI';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // http://localhost:3000/song
        $data = [];
        $updated = [];
        $songs = Song::all();
        $base_url = 'http://localhost:3000/music/update?id=';

        $id = $this->argument('id');
        if ($id !== null) {
            $song = Song::findOrFail($id);

            if ($song !== null) {
                $this->updateSong($base_url, $song, $songs, $updated);

                return 0;
            }

            return 0;
        }

        /** @var Song $song */
        foreach ($songs as  $song) {
            if ($song->image === null) {
                $total = $this->updateSong($base_url, $song, $songs, $updated);
                sleep(20);
            } else {
                $updated[] = $song->title;
            }

            $data[] = [
                'id' => $song->id,
                'title' => $song->title,
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
}
