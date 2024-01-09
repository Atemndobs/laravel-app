<?php

namespace App\Console\Commands\Song;

use App\Models\Song;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Stancl\Tenancy\Events\DatabaseDeleted;
use const example\int;

class SongCleanUpCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:cleanup {--e|exist=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        // get all songs files from the s3 bucket music folder
        $uploadedSongs = Storage::disk('s3')->files('music');
        $uploadedSongsCount = count($uploadedSongs);
        $this->info("Found {$uploadedSongsCount} songs in the s3 bucket");

        // Extract the slug from the songs in the s3 bucket and
        $uploadedSongsSlugs = [];
        foreach ($uploadedSongs as $uploadedSong) {
            $uploadedSongSlug = str_replace('music/', '', $uploadedSong);
            $uploadedSongSlug = str_replace('.mp3', '', $uploadedSongSlug);
            $uploadedSongsSlugs[] = $uploadedSongSlug;
        }
        //check if they exist in the database
        $songs = Song::query()->get();
        $songsCount = $songs->count();

        $songsSlugs = $songs->pluck('slug')->toArray();
        // get the difference between the songs in the database and the songs in the s3 bucket
        $deletableSongs = array_diff($songsSlugs, $uploadedSongsSlugs);
        $deletableSongsCount = count($deletableSongs);
        $info = [
            'songs in the database' => $songsCount,
            'songs in the s3 bucket' => $uploadedSongsCount,
            'songs to delete' => $deletableSongsCount,
        ];
        $infoMessage = json_encode($info, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $this->line("<fg=yellow>$infoMessage</>");
        // for each deletable file in the database, check if it is analyzed. If it is analyzed, keep it
        $deletableSongs = Song::query()->whereIn('slug', $deletableSongs)->get();
        /** @var Song $deletableSong */
        foreach ($deletableSongs as $deletableSong) {
            if ($deletableSong->analyzed && (int)$deletableSong->song_id !== null) {
                $this->info("Song $deletableSong->title is analyzed & has a song ID");
                $deletableSong->status = 'download';
                $deletableSong->save();
            } else {
                $this->info("Song $deletableSong->title is not analyzed");
                $deletableSong->status = 'delete';
            }
        }

        // for each $songSlug in database, check if ii is analyzed. If it is analyzed, keep it

        dd('END OF SCRIPT');
        $this->markDuplicateSongs();
        $bar = $this->output->createProgressBar(count($duplicateSongs));
        if ($this->option('exist')) {
            $this->info('Cleaning up songs that dont exist from the database');
            // progress bar start
            $bar = $this->output->createProgressBar(count($deletableSongs));
            $songs = Song::all();
            foreach ($songs as $song) {
                $url = str_replace('mage.tech:8899', 'nginx', $song->path);
                $filename = basename($url);
                $this->info("checking $filename");
                $fileExists = file_exists("/var/www/html/storage/app/public/audio/$filename");
                if (!$fileExists) {
                    $song->delete();
                    $this->output->info("$song->title : deleted");
                }

                $bar->advance();
            }

        }
        $bar->finish();
        return 0;
    }

    /**
     * @return \Symfony\Component\Console\Helper\ProgressBar
     */
    public function markDuplicateSongs(): \Symfony\Component\Console\Helper\ProgressBar
    {
        $duplicateSongs = Song::query()->select('slug')->groupBy('slug')->havingRaw('count(*) > 1')->get();
        $this->info("Found {$duplicateSongs->count()} duplicate songs");

        // dd($duplicateSongs);
        $bar = $this->output->createProgressBar(count($duplicateSongs));

        dump(count($duplicateSongs));
        $duplicateSongs->each(function ($song) use ($bar) {
            $songs = Song::query()->where('slug', $song->slug)->get();
            // for each duplicate  song check if it is analyzed. If moe than one is analyzed, keep the one with a working path
            $analyzedSongs = $songs->filter(function ($song) {
                return $song->analyzed;
            });
            $this->warn("Found {$analyzedSongs->count()} analyzed songs with slug $song->slug");
            if ($analyzedSongs->count() > 1) {
                $this->info("Found {$analyzedSongs->count()} analyzed songs with slug $song->slug");
                $analyzedSongs->each(function ($song) {
                    $this->info("Song $song->title is analyzed");
                });
                $this->info("Keeping the one with a working path");
                $analyzedSongsWithWorkingPath = $analyzedSongs->filter(function ($song) {
                    if (Http::get($song->path)->ok()) {
                        $messageWorking = [
                            'song' => $song->title,
                            'path' => $song->path,
                            'slug' => $song->slug,
                            'Analyzed songs' => "== slug: $song->slug has a working path ==",
                        ];
                        $this->info(json_encode($messageWorking, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                        return true;
                    }
                    // return $song->path !== null && $song->path !== '';
                    return false;
                });
                if ($analyzedSongsWithWorkingPath->count() > 0) {
                    $this->info("Found {$analyzedSongsWithWorkingPath->count()} analyzed songs with working path");
                    $analyzedSongsWithWorkingPath->each(function ($song) {
                        $messageWorking = [
                            'song' => $song->title,
                            'path' => $song->path,
                            'slug' => $song->slug,
                            'Many songs With working paths' => "== slug: $song->slug has a working path ==",
                        ];
                        $this->info(json_encode($messageWorking, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                    });
                    $songToKeep = $analyzedSongsWithWorkingPath->first();
                    $this->info("Keeping song $songToKeep->title");
                    $songs->each(function ($song) use ($songToKeep) {
                        if ($song->id !== $songToKeep->id) {
                            //  $song->delete();
                            $song->status = 'duplicate';
                            $song->save();
                            $messageDelete = [
                                'song' => $song->title,
                                'path' => $song->path,
                                'slug' => $song->slug,
                                'Song to delete' => "== slug: $song->slug has a working path ==",
                            ];
                            $this->info(json_encode($messageDelete, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                        }
                    });
                } else {
                    $this->info("None of the analyzed songs have a working path");
                    $this->info("Keeping the first analyzed song");
                    $songToKeep = $analyzedSongs->first();
                    $this->info("Keeping song $songToKeep->title");
                    $songs->each(function ($song) use ($songToKeep) {
                        if ($song->id !== $songToKeep->id) {
                            $song->status = 'duplicate';
                            $song->save();
                            $messageDelete = [
                                'song' => $song->title,
                                'path' => $song->path,
                                'slug' => $song->slug,
                                'Song to delete' => "== slug: $song->slug has a working path ==",
                            ];
                            $this->info(json_encode($messageDelete, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                        }
                    });
                }
            } else {
                $this->info("Keeping the first song");
                $songToKeep = $songs->first();
                $this->info("Keeping song $songToKeep->title");
                $songs->each(function ($song) use ($songToKeep) {
                    if ($song->id !== $songToKeep->id) {
                        $song->status = 'duplicate';
                        $song->save();
                        $messageDelete = [
                            'song' => $song->title,
                            'path' => $song->path,
                            'slug' => $song->slug,
                            'Song to delete' => "== slug: $song->slug has a working path ==",
                        ];
                        $this->info(json_encode($messageDelete, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                    }
                });
            }
            $songs->each(function ($song) {
                // $song->delete();
                $this->output->info("$song->title : deleted");
            });
            $bar->advance();
        });
        return $bar;
    }
}
