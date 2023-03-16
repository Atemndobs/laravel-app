<?php

namespace App\Console\Commands\Song;

use App\Models\Song;
use App\Services\SongUpdateService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use function example\int;

class SongUpdatePathCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:path {slug?} {--a|all=false} {--d|dir=}';

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
        $slug = $this->argument('slug');
        $all = $this->option('all');
        $dir = $this->option('dir');
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

        $missingSongs = [];
        /** @var Song $song */
        foreach ($songs as $song) {
            $fileName = basename($song->path);
            if ($dir === 'music') {
                if (!Storage::cloud()->exists("$dir/" . $fileName)) {
                    $this->error("file not found | " . $fileName);
                    $missingSongs[] = $song->title ." | " .  $fileName;
                    continue;
                }
                $songPath = Storage::cloud()->url("curator/$dir/" . $fileName);
                $this->info("new path | " . $songPath);
                $song->path = $songPath;
                $song->save();
                continue;
            }

            if ($dir === 'images') {
                if (!Storage::cloud()->exists("$dir/" . $fileName)) {
                    $this->error("No Image found for  | " . $fileName);
                    $missingSongs[] = $song->title ." | " .  $fileName;
                    continue;
                }
                $songPath = Storage::cloud()->url("curator/$dir/" . $fileName);
                $this->info("Uploading Image for | " . $songPath);
                $song->image = $songPath;
                $song->save();
            }

        }
        dump($missingSongs);

        return 0;
    }
}
