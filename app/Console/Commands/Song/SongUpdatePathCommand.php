<?php

namespace App\Console\Commands\Song;

use App\Models\Setting;
use App\Models\Song;
use App\Services\SongUpdateService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Psy\Util\Str;
use function example\int;
use function PHPUnit\Framework\isFalse;

class SongUpdatePathCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:path {slug?} {--a|all=false} {--d|dir=} {--f|field=} {--i|identifier=} {--m|modifier=} {--r|dry-run=false}}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Song path to point to new storage';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $slug = $this->argument('slug');
        $all = $this->option('all');
        $dir = $this->option('dir');
        $field= $this->option('field');
        $dryRun = $this->option('dry-run');
        $identifier = $this->option('identifier');
        $modifier = $this->option('modifier');

       // $this->info("starting | " . $slug . " | " . $all . " | " . $dir . " | " . $field . " | " . $dryRun . " | " . $identifier) ;

        if (strlen($slug) === 0 && $all === false) {
            $this->info('No slug provided');
            return 0;
        }
        if ($all !== false) {
            $songs = Song::query()->get();
        } else {
            $songs = Song::query()->where('slug', '=', "$slug")->get();
        }

        // get s3 base url and s3 bucket from the settings table
        $base_url = Setting::query()->where('key', 'base_url')->first()->value;
        $bucket = Setting::query()->where('key', 'bucket')->first()->value;
        $base_aws_url = Setting::query()->where('key', 'base_aws_url')->first()->value;


        if ($dryRun !== "false") {
            $this->info('Dry run results');
            $this->table(['field', 'identifier', 'modifier', 'total', 'all?', 'dir', 'slug'], [
                [
                    'field' => $field,
                    'identifier' => $identifier,
                    'modifier' => $modifier,
                    'total' => $songs->count(),
                    'all' => $all,
                    'dir' => $dir,
                    'slug' => $slug
                ]
            ]);

            return 0;
        }
//        if ($field !== null) {
//            // start the progress bar
//            $this->output->progressStart($songs->count());
//            // get all songs that contain $identifier in the $field and replace the identifier with the new base url
////            /** @var Song $song */
////            $relatedSongs = $songs->toQuery()->where('related_songs', 'like', "%$identifier%")->get()->map(function ($song) use ($field, $identifier, $base_url) {
////                $this->output->progressAdvance();
////                $relatedSongsPath = $song->related_songs;
////                // extract part of url starting with api including 'api'
////                $endpoint = substr($relatedSongsPath, strpos($relatedSongsPath, 'api'));
////                // append base url to endpoint
////                $relatedSongsPath = $base_url . '/' . $endpoint;
////                $song->related_songs = $relatedSongsPath;
////                $song->save();
////               // $this->info("updated | " . $song->related_songs);
////            });
////
//            // finish the progress bar
//            $this->output->progressFinish();
//            // create table with results
//            $this->table(['field', 'identifier', 'modifier', 'total'], [
//                [
//                    'field' => $field,
//                    'identifier' => $identifier,
//                    'modifier' => $modifier,
//                    'total' => $songs->count(),
//                ]
//            ]);
//
//            info(json_encode([
//                [
//                    'field' => $field,
//                    'identifier'=> $identifier,
//                    'modifier' => $modifier,
//                    'total' => $songs->count(),
//                ]
//            ]));
//        }

        // start progress bar
        $this->output->progressStart($songs->count());
        $missingSongs = [];
        /** @var Song $song */
        foreach ($songs as $song) {
            $fileName = basename($song->path);
            $newPath = $base_url . '/' . $bucket . '/' . $dir . '/' . $fileName;
            if ($dir === 'music') {
                $song->path = $newPath;
                $song->save();

                // continue progress bar
                $this->output->progressAdvance();
                $message = [
                    'field' => $field,
                    'identifier' => $identifier,
                    'modifier' => $modifier,
                    'songs_id' => $song->id
                ];
            $this->table(['field', 'identifier', 'modifier', 'songs_id'], [$message]);
            info(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
//                dd([
//                    'filename' => $fileName,
//                    'song' => $song->title,
//                    'path' => $song->path,
//                    'newPath' => $newPath,
//                    'aws_url' => $base_aws_url . '/' . $dir . '/' . $fileName,
//                    'base_aws_url' => $base_aws_url,
//                    'slug' => $song->slug,
//                    'id' => $song->id
//                ]);
            }

//            if ($dir === 'images') {
//
//                $songImage = $song->image;
//                // if image is not set, skip
//                $this->info("Image is not set | " . $song->image);
//                $imageName = \Illuminate\Support\Str::slug($fileName , '_');
//                $imageName = $imageName. '.jpeg';
//                $songPath = Storage::cloud()->url("$dir/" . $imageName);
//                if ($songImage === null || $songImage === '') {
//
//                    $this->info("Uploading Image for | " . $songPath);
//                    $req = Http::get($songPath);
//                    if (!$req->successful()) {
//                        $this->error("No Image found for  | " . $fileName);
//                        $missingSongs[] = $song->title ." | " .  $fileName;
//                        $song->image = null;
//                        $song->save();
//                    }else{
//                        $this->info("Image is good | " . $songPath);
//                        $song->image = $songPath;
//                        $song->save();
//                    }
//                    continue;
//                }
//                $imageUrlCheck = Http::get($songImage);
//
//                if ($imageUrlCheck->successful()) {
//                    $this->info("Image is good | " . $song->image);
//                    continue;
//                }else{
//                    $this->error("No Image found for  | " . $fileName);
//                    $missingSongs[] = $song->title ." | " .  $fileName;
//                    $song->image = null;
//                    $song->save();
//                }
//
//
//            }

        }
        Log::info(json_encode([
            'allSongs' => $songs->count(),
            'missingSongs' => count($missingSongs)
        ] , JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        info(json_encode($missingSongs));

        return 0;
    }
}
