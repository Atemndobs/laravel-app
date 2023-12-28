<?php

namespace App\Console\Commands;

use App\Models\Song;
use Illuminate\Console\Command;
use Illuminate\Support\Benchmark;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ListFixer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix {--p|path=} {--s|source=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix Song links to point to aws s3 bucket';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
       $this->fixImages();

       dd();
    
       $path = $this->option('path') ?? 'music';
       $source = $this->option('source') ?? 'audio';
       $this->uploadToAwsS3($path, $source);
       return 0;
    }

    /**
     * Fix the songs by updating their paths to point to the AWS S3 bucket.
     *
     * @return void
     */
    private function fixSongs()
    {
        // get all songs that are not on AWS S3 bucket and update them. These songs have a path starting with http://s3.atemkeng.de:9000
        $allSongs = Song::query()->get();
        $this->info("Found {$allSongs->count()} songs in the database");
        $songs = Song::query()->where('path', 'like', 'http://s3.atemkeng.de:9000%')->get();
        // update the path to point to the AWS S3 bucket in the format "https://s3.amazonaws.com/curators3/music/" +  $song->slug
        $count = $songs->count();
        $fixedSongs = [];
        $this->info("Found $count songs to fix");
        // progress bar start
        $bar = $this->output->createProgressBar(count($songs));
        $songs->each(function ($song) use (&$fixedSongs, $bar) {
            // if slug ends with mp3, remove it
            if (Str::endsWith($song->slug, 'mp3')) {
                $song->slug = Str::replaceLast('mp3', '', $song->slug);
            }
            $song->path = "https://s3.amazonaws.com/curators3/music/" . $song->slug . '.mp3';
            $song->save();
            $fixedSongs[] = $song->slug;
            $countFixed = count($fixedSongs);
            $this->info("Fixed $countFixed songs");
            $this->warn("Fixed {$song->slug}");
            $bar->advance();
        });
        $bar->finish();
        $message = [
            'fixedSongs' => count($fixedSongs),
            'totalSongs' => $count,
        ];
        $this->info(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ));
        $this->info('==============Done================');
    }


    private function uploadToAwsS3($dir = 'music', $source = 'audio')
    {
        $files = Storage::disk('public')->files($source);
        $count = count($files);
        $this->info("Found $count files");
        $bar = $this->output->createProgressBar(count($files));
        $slugs = [];
        foreach ($files as $file) {
            // make the files this format: /var/www/html/storage/app/public/audio/015b_not_bad_feat_dawon.mp3
            $file = '/var/www/html/storage/app/public/' . $file;
            // upload to aws s3
            $this->info("Uploading $file to aws s3");
            // call s3:put {--f|file=} {--d|directory=music}
            $this->call('s3:put', [
                '--file' => $file,
                '--directory' => $dir
            ]);

            $slugs[] = $file;   
            $bar->advance();
        }
        $bar->finish();
        $this->info("Found " . count($slugs) . " slugs");
        $message = [
            'slugsList' => $slugs,
            'totalFiles' => $count,
        ];
        $this->info(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ));
    }

    //function to fix image links
    private function fixImages()
    {
        $songs = Song::query()->where('image', 'like', 'http://s3.atemkeng.de:9000%')->get();
        $count = $songs->count();
        $fixedSongs = [];
        $this->info("Found $count songs to fix");
        // progress bar start
        $bar = $this->output->createProgressBar(count($songs));
        $songs->each(function ($song) use (&$fixedSongs, $bar) {
            if (Str::startsWith($song->image, 'https://i.scdn.co/image/')) {
                $bar->advance();
                return;
            }

            // $file_name = last part of song->image
            $file_name = last(explode('/', $song->image));
            $song_image = "https://s3.amazonaws.com/curators3/images/" . $file_name ;
            $song->image = $song_image;
            $song->save();
            $fixedSongs[] = $song->slug;
            $countFixed = count($fixedSongs);
            $this->info("Fixed $countFixed songs");
            $this->warn("Fixed {$song->slug}");
            $bar->advance();
        });
        $bar->finish();
        $message = [
            'fixedSongs' => count($fixedSongs),
            'totalSongs' => $count,
        ];
        $this->info(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ));
        $this->info('==============Done================');
    }
    
}
