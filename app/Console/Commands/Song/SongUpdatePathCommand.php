<?php

namespace App\Console\Commands\Song;

use App\Models\Song;
use App\Services\SongUpdateService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
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
                $songPath = Storage::cloud()->url("curator/$dir/" . $fileName);
                $this->info("new path | " . $songPath);
                $song->path = $songPath;
                $song->save();
                continue;
            }

            if ($dir === 'images') {

                $imageName = \Illuminate\Support\Str::slug($fileName , '_');
                $imageName = $imageName. '.jpeg';


                $songPath = Storage::cloud()->url("curator/$dir/" . $imageName);
                $this->info("Uploading Image for | " . $songPath);

                $req = Http::get($songPath);
                // check if image exists
                if (!$req->successful()) {
                    $this->error("No Image found for  | " . $fileName);
                    $missingSongs[] = $song->title ." | " .  $fileName;
                    $song->image = null;
                    $song->save();
                    continue;
                }
                $song->image = $songPath;
                $song->save();
            }

        }
        dump($missingSongs);
        dump([
            'allSongs' => $songs->count(),
            'missingSongs' => count($missingSongs),
        ]);
        info(json_encode($missingSongs));

        return 0;
    }
}
