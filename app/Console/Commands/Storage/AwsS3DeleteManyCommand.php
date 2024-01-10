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

        // get paths of songs where bpm = 0. for each or the paths, call the s3:delete command
        foreach ($songs as $song) {
            $this->info("deleting song |  ".$song->slug);
            $this->call('s3:delete', [
                '--file' => $song->path,
                '--directory' => 'music',
            ]);
        }
        $message = [
            'status' => 'success',
            'Total songs deleted' => count($songs),
            'message' => 'File deleted successfully from ' . env('AWS_BUCKET') . '/' . $directory,
        ];
        $this->info(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        return 0;
    }
}
