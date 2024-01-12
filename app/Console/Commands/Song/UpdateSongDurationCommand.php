<?php

namespace App\Console\Commands\Song;

use App\Models\Song;
use App\Services\SongUpdateService;
use Illuminate\Console\Command;

class UpdateSongDurationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:duration {slug?} {--f|field=null}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update song Duration description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $updateService = new SongUpdateService();
        $slug = $this->argument('slug');
        if ($slug !== null) {
            $this->info("prepare updating |  $slug");
            $results = $updateService->updateDuration($slug);
        }

        // no slug is given so get all songs wth no duration and update them
        $songs = Song::query()->whereNull('duration')->get();
        //$this->info("prepare updating |  {$songs->count()} songs");
        $resultsWithSameSlug = [];
        $noDurationSoundcloudSongs = Song::query()->whereNull('duration')->where('source', 'soundcloud')->get();
        $noDurationSpotifySongs = Song::query()->whereNull('duration')->where('source', 'spotify')->get();
        dump([
            'songs_count' => $songs->count(),
            'no_duration_souncloud_songs_count' => count($noDurationSoundcloudSongs),
            'no_duration_spotify_songs_count' => count($noDurationSpotifySongs),
        ]);

        $songResults = [];
        $foundSongs = [];
        $songsWithNoSource = [];
        $songsWithSource = [];
        foreach ($noDurationSoundcloudSongs as $noDurationSoundcloudSong) {
            $this->info("Updating {$noDurationSoundcloudSong->slug}");
            // check if there are moe songs in db with the same slug
            $songsWithSameSlug = Song::query()->where('slug', $noDurationSoundcloudSong->slug)
                ->get();
            if ($songsWithSameSlug->count() >= 2) {
               // dd($songsWithSameSlug->toArray());
                $moreThanOneSongWithSameSlug[] = [
                    'slug' => $noDurationSoundcloudSong->slug,
                    'count' => $songsWithSameSlug->count(),
                ];
                // if the soung has no source , then  call it $songWithNoSource and if it has source then call it $songWithSource
                $songWithNoSource = $songsWithSameSlug->where('source', '!=',  'soundcloud')->first();
                $songWithSource = $songsWithSameSlug->where('source', 'soundcloud')->first();
                if ($songWithSource) {
                    $songsWithSource[] = [
                        'title' => $songWithSource->title,
                        'author' => $songWithSource->author,
                        'duration' => $songWithSource->duration,
                        'slug' => $songWithSource->slug,
                        'source' => $songWithSource->source, // 'soundcloud' or 'spotify
                        'song_url' => $songWithSource->song_url,
                        'song_id' => $songWithSource->song_id, // 'soundcloud' or 'spotify
                        'analyszed' => $songWithSource->analyszed,
                        'energy' => $songWithSource->energy,
                    ];
                }
                if ($songWithNoSource) {
                    //dump('Got song with no source');
                    $songsWithNoSource[] = [
                        'title' => $songWithNoSource->title,
                        'author' => $songWithNoSource->author,
                        'duration' => $songWithNoSource->duration,
                        'slug' => $songWithNoSource->slug,
                        'source' => $songWithNoSource->source, // 'soundcloud' or 'spotify
                        'song_url' => $songWithNoSource->song_url,
                        'song_id' => $songWithNoSource->song_id, // 'soundcloud' or 'spotify
                        'analyszed' => $songWithNoSource->analyszed,
                        'energy' => $songWithNoSource->energy,
                    ];
                    $songWithNoSource->song_url = $songWithSource->song_url;
                    $songWithNoSource->song_id = $songWithSource->song_id;
                    $songWithNoSource->source = $songWithSource->source;
                    $songWithNoSource->save();
                }
            }
        }

        dump([
            'soundcloud_songs_count' => count($noDurationSoundcloudSongs),
            'spotify_songs_count' => count($noDurationSpotifySongs),
            'song_with_source_count' => count($songsWithSource),
            'song_with_no_source_count' => count($songsWithNoSource),
        ]);

        // do the same for spotify songs
        foreach ($noDurationSpotifySongs as $noDurationSpotifySong) {
            $this->info("Updating {$noDurationSpotifySong->slug}");
            // check if there are moe songs in db with the same slug
            $songsWithSameSlug = Song::query()->where('slug', $noDurationSpotifySong->slug)
                ->get();
            if ($songsWithSameSlug->count() >= 2) {

                dd('SPOTIFY');
                // if the soung has no source , then  call it $songWithNoSource and if it has source then call it $songWithSource
                $songWithNoSource = $songsWithSameSlug->where('source', '!=', 'spotify')->first();
                $songWithSource = $songsWithSameSlug->where('source', 'spotify')->first();
                if ($songWithSource) {
                    $songsWithSource[] = [
                        'title' => $songWithSource->title,
                        'author' => $songWithSource->author,
                        'duration' => $songWithSource->duration,
                        'slug' => $songWithSource->slug,
                        'source' => $songWithSource->source, // 'soundcloud' or 'spotify
                        'song_url' => $songWithSource->song_url,
                        'song_id' => $songWithSource->song_id, // 'soundcloud' or 'spotify
                        'analyszed' => $songWithSource->analyszed,
                        'energy' => $songWithSource->energy,
                    ];
                }
                if ($songWithNoSource) {
                    dump('Got song with no source');
                    $songsWithNoSource[] = [
                        'title' => $songWithNoSource->title,
                        'author' => $songWithNoSource->author,
                        'duration' => $songWithNoSource->duration,
                        'slug' => $songWithNoSource->slug,
                        'source' => $songWithNoSource->source, // 'soundcloud' or 'spotify
                        'song_url' => $songWithNoSource->song_url,
                        'song_id' => $songWithNoSource->song_id, // 'soundcloud' or 'spotify
                        'analyszed' => $songWithNoSource->analyszed,
                        'energy' => $songWithNoSource->energy,
                    ];
//                    $songWithNoSource->song_url = $songWithSource->song_url;
//                    $songWithNoSource->song_id = $songWithSource->song_id;
//                    $songWithNoSource->source = $songWithSource->source;
//                    $songWithNoSource->save();
                }

            }
        }
        dd([
            'songWithNoSource_count' => count($songsWithNoSource),
            'songWithSource_count' => count($songsWithSource),

        ]);


        // start bar
        $this->withProgressBar($songs, function ($song) use ($updateService, &$results, &$resultsWithSameSlug) {
            try {
//                $this->line("");
//                $this->info("Updating {$song->slug}");
//                $results = $updateService->updateDuration($song->slug);

                // find all songs with the same slug and source = Soundcloud and id not = $song->id and update them
                $songsWithSameSlug = Song::query()->where('slug', $song->slug)
                    ->where('source', 'soundcloud')
                    ->whereNot('id', $song->id)
                    ->get();
                // if the $songsWithSameSlug is not empty then update them
                $results = [
                    'title' => $song->title,
                    'author' => $song->author,
                    'duration' => $song->duration,
                    'slug' => $song->slug,
                    'image' => $song->image,
                    'path' => $song->path,
                ];
               // $this->line(json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));


            }catch (\Exception $e){
                $message = [
                    'error' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile(),
                ];
                $this->line("</fg=red>". json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ."</>");
            }
        });

        $res = [
            'songsWithSameSlug' => $resultsWithSameSlug,
            'count_songs_with_same_slug' => count($resultsWithSameSlug),
            'count_songs' => $songs->count(),
        ];

        $this->info(json_encode($res, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

//        try {
//            $this->table(['title','author',  'duration', 'slug', 'image'], [$results]);
//        }catch (\Exception $e){
//            $this->info("Image From $slug");
//            $this->table(['title','author',  'duration', 'slug', 'image'], $results);
//        }
        return 0;
    }
}
