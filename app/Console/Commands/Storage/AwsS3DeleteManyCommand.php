<?php

namespace App\Console\Commands\Storage;

use App\Models\Song;
use Illuminate\Console\Command;
use App\Services\Storage\AwsS3Service;

class AwsS3DeleteManyCommand extends Command
{
    /**
     * Execute the console command.
     */
    protected $signature = 's3:delete-multi {--f|file=} {--d|directory=music}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Uploads a file to S3';

    public function handle()
    {
        $file = $this->option('file');
        $directory = $this->option('directory');

        if (!$directory ) {
            $this->error('A file must be specified with -f option.');
            return 1;
        }
        $songs = Song::query()->where('bpm', '=', 0)->get();
        $message = [
            'Total songs to delete' => count($songs),
            'location' => env('AWS_BUCKET') . '/' . $directory,
        ];
        $this->warn(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $downloadedSongs = [];
        /** @var Song $song */
        foreach ($songs as $song) {
//            $this->info("Downloading song |  ".$song->song_url);
//            if ($song->song_url === null) {
//                $this->error("Song url is null |  ".$song->song_url);
//                continue;
//            }
//            // if source soundcloud then call scrap:sc command and pass the song url as -l and -c option
//            if (str_contains($song->source, 'soundcloud')) {
//                $this->call('scrape:sc', [
//                    '--link' => $song->song_url,
//                    '--continue' => true,
//                ]);
//                $downloadedSongs[] = $song->slug;
//                // write the slug to a file downloaded.txt
//                file_put_contents('downloaded.txt', $song->slug . "\n", FILE_APPEND);
//            }
//            // if source is spotify then call the spotify command and pass the song url adn -f option
//            if (str_contains($song->source, 'spotify')) {
//                $this->call('spotify', [
//                    'url' => $song->song_url,
//                    '--force' => true,
//                ]);
//                $downloadedSongs[] = $song->slug;
//                // write the slug to a file downloaded.txt
//                file_put_contents('downloaded.txt', $song->slug . "\n", FILE_APPEND);
//            }
//            $this->call('s3:delete', [
//                '--file' => $song->path,
//                '--directory' => 'music',
//            ]);
        }
        $message = [
            'status' => 'success',
            'Total songs to delete' => count($songs),
            'Deleted songs count' => count($downloadedSongs),
            'message' => 'File deleted successfully from ' . env('AWS_BUCKET') . '/' . $directory,
        ];
        $this->info(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        return 0;
    }
}
