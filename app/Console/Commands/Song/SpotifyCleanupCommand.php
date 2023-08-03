<?php

namespace App\Console\Commands\Song;

use App\Services\Storage\MinioService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SpotifyCleanupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:spotify-clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add spotify url to songs with song_id and source spotify.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // find all songs with song_id and source spotify
        $songs = \App\Models\Song::query()
            ->whereNotNull('song_id')
            ->where('source', 'spotify')
            ->get();
        // check for duplicate song_ids
        $songIds = $songs->pluck('song_id')->toArray();
        $duplicates = array_unique(array_diff_assoc($songIds, array_unique($songIds)));
        // create a array of content of all duplicates and group them by song_id and include the song title and author
        $duplicatesContent = [];
        foreach ($duplicates as $duplicate) {
            $duplicatesContent[$duplicate] = $songs->filter(function ($song) use ($duplicate) {
                return $song->song_id === $duplicate;
            })->map(function ($song) {
                // return only the title and author of the song
                return [
                    'id' => $song->id ?? 'null',
                    'title' => $song->title ?? 'null',
                    'author' => $song->author ?? 'null',
                    'path' => $song->path ?? 'null',
                    'image' => $song->image ?? 'null',
                    'genre' => $song->genre ?? 'null',
                    'bpm' => $song->bpm ?? 'null',
                ];
              //  return $song->title . ' by ' . $song->author;
            })->toArray();
        }

        $deletables = [];
        $retainables = [];
        foreach ($duplicatesContent as $key => $duplicates) {
            // rename the keys of the items in duplicates array to start from 0
            $duplicates = array_values($duplicates);
            if (count($duplicates) === 1) {
                unset($duplicatesContent[$key]);
            }
            // collect all genres from duplicates into 1 array
            $genres = [];

            foreach ($duplicates as $duplicateKey => $duplicate) {
                if ($duplicateKey === 0) {
                    $retainables[] = $duplicate;
                }
                // add all other duplicates to deletables array
                if ($duplicateKey !== 0) {
                    $deletables[] = $duplicate;
                }

                if (is_array($duplicate['genre'])) {
                    $genres = array_merge($genres, $duplicate['genre']);
                    continue;
                }

                $genres[] = $duplicate['genre'];
            }
            $genres = array_unique($genres);
            $retainables[0]['genre'] = $genres;

            $duplicatesContent[$key] = [
                'id' => $key,
                'deletables' => $deletables,
                'retainables' => $retainables,
            ];

        }

        foreach ($retainables as $retainable) {
            $song = \App\Models\Song::find($retainable['id']);
            $song->genre = $retainable['genre'];
            $song->save();
            $this->info('SONG ID ' . $retainable['id'] . ' RETAINED') ;
            $this->info('Genre: ' . json_encode($retainable['genre'], JSON_PRETTY_PRINT));
        }
        foreach ($deletables as $deletable) {
            $id = $deletable['id'];
            $sql = "DELETE FROM songs WHERE id = $id";
            DB::statement($sql);
            $this->warn('SONG ID ' . $deletable['id'] . ' DELETED') ;
            $this->warn('Deleted song ' . $deletable['title'] . ' by ' . $deletable['author']);
        }

        dd([
            'songs_count' => $songs->count(),
            'duplicate_songs_count' => count($duplicatesContent),
            'retainables_count' => count($retainables),
            'deletables_count' => count($deletables),
           // 'duplicates_content' => $duplicatesContent,
        ]);
    }
}
