<?php

namespace App\Console\Commands\Song;

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
    protected $signature = 'song:path {slug?} {--a|all=false} {--d|dir=} {--f|field=} {--i|identifier=} {--r|dry-run=false}}';

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

        $this->info("starting | " . $slug . " | " . $all . " | " . $dir . " | " . $field . " | " . $dryRun . " | " . $identifier) ;

        if (strlen($slug) === 0 && $all === false) {
            $this->info('No slug provided');
            return 0;
        }
        if ($all !== false) {
            $songs = Song::query()->get();
        } else {
            $songs = Song::query()->where('slug', '=', "$slug")->get();
        }

        $dir = $dir ?? 'music';
        $identifier = $identifier ?? 'mage.tech:8899';
        $base_url = env('APP_ENV') == 'local' ? 'http://mage.tech:8899' : env('APP_URL');

        if ($dryRun !== "false") {
            $this->info('Dry run results');
            $this->table(['field', 'identifier', 'related', 'total', 'all?', 'slug', 'dir'], [
                [
                    'field' => $field,
                    $identifier => $identifier,
                    'related' => $songs->toQuery()->where('related_songs', 'like', "%$identifier%")->get()->count(),
                    'total' => $songs->count(),
                    'all' => $all,
                    'slug' => $slug,
                    'dir' => $dir,
                ]
            ]);
            
            return 0;
        }

        if ($field !== null) {
            // start the progress bar
            $this->output->progressStart($songs->count());
            // get all songs that contain $identifier in the $field and replace the identifier with the new base url
            /** @var Song $song */
            $relatedSongs = $songs->toQuery()->where('related_songs', 'like', "%$identifier%")->get()->map(function ($song) use ($field, $identifier, $base_url) {
                $this->output->progressAdvance();
                $relatedSongsPath = $song->related_songs;
                // extract part of url starting with api including 'api'
                $endpoint = substr($relatedSongsPath, strpos($relatedSongsPath, 'api'));
                // append base url to endpoint
                $relatedSongsPath = $base_url . '/' . $endpoint;
                $song->related_songs = $relatedSongsPath;
                $song->save();
               // $this->info("updated | " . $song->related_songs);
            });
            // finish the progress bar
            $this->output->progressFinish();
            // create table with results
            $this->table(['field', 'identifier', 'related', 'total'], [
                [
                    'field' => $field,
                    $identifier => $identifier,
                    'related' => $relatedSongs->count(),
                    'total' => $songs->count(),
                ]
            ]);

            info(json_encode([
                [
                    'field' => $field,
                    $identifier => $identifier,
                    'related' => $relatedSongs->count(),
                    'total' => $songs->count(),
                ]
            ]));
            return 0;
        }

        $missingSongs = [];
        /** @var Song $song */
        foreach ($songs as $song) {
            $fileName = basename($song->path);
            if ($dir === 'music') {
                $songUrlCheck = Http::get($song->path);
                if ($songUrlCheck->successful()) {
                    $this->info("Song is good : Skipping | " . $song->path);
                    continue;
                }
                $songPath = Storage::cloud()->url("$dir/" . $fileName);
                dd($songPath);
                $req = Http::get($songPath);
                if (!$req->successful()) {
                    $this->error("No Song found for  | " . $fileName);
//                    $song->path = '';
//                    $song->save();
                    $missingSongs[] = $song->title ." | " .  $fileName;
                }else{
                    $this->info("Path is good | " . $songPath);
                    $song->path = $songPath;
                    $song->save();
                    continue;
                }
            }

            if ($dir === 'images') {

                $songImage = $song->image;
                // if image is not set, skip
                $this->info("Image is not set | " . $song->image);
                $imageName = \Illuminate\Support\Str::slug($fileName , '_');
                $imageName = $imageName. '.jpeg';
                $songPath = Storage::cloud()->url("$dir/" . $imageName);
                if ($songImage === null || $songImage === '') {
     
                    $this->info("Uploading Image for | " . $songPath);
                    $req = Http::get($songPath);
                    if (!$req->successful()) {
                        $this->error("No Image found for  | " . $fileName);
                        $missingSongs[] = $song->title ." | " .  $fileName;
                        $song->image = null;
                        $song->save();
                    }else{
                        $this->info("Image is good | " . $songPath);
                        $song->image = $songPath;
                        $song->save();
                    }
                    continue;
                }
                $imageUrlCheck = Http::get($songImage);
                
                if ($imageUrlCheck->successful()) {
                    $this->info("Image is good | " . $song->image);
                    continue;
                }else{
                    $this->error("No Image found for  | " . $fileName);
                    $missingSongs[] = $song->title ." | " .  $fileName;
                    $song->image = null;
                    $song->save();
                }
                

            }

        }
        Log::info(json_encode([
            'allSongs' => $songs->count(),
            'missingSongs' => count($missingSongs)
        ] , JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        info(json_encode($missingSongs));

        return 0;
    }
}
