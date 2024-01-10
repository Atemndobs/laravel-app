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

        // get paths of songs where bpm = 0. for each or the paths, call the s3:delete command
        $songs = Song::query()->where('bpm', '=', 0)->get();
        foreach ($songs as $song) {
            $this->info("deleting song |  ".$song->slug);
            $this->call('s3:delete', [
                '--file' => $song->path,
                '--directory' => 'music',
            ]);
        }
        $message = [
            'status' => 'success',
            'message' => 'File deleted successfully from ' . env('AWS_BUCKET') . '/' . $directory,
        ];
        $this->info(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        return 0;
    }
}
