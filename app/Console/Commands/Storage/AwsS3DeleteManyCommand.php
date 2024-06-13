<?php

namespace App\Console\Commands\Storage;

use App\Models\Song;
use Illuminate\Console\Command;
use App\Services\Storage\AwsS3Service;
use Illuminate\Support\Facades\Artisan;

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

    private AwsS3Service $s3Service;

    public function __construct(AwsS3Service $s3Service)
    {
        parent::__construct();
        $this->s3Service = $s3Service;
    }

    public function handle()
    {
        $file = $this->option('file');
        $directory = $this->option('directory');

        $filePath = ("{$file}");

        $file_name = basename($filePath);
        $key = "{$directory}/{$file_name}";



        if (!$directory ) {
            $this->error('A file must be specified with -f option.');
            return 1;
        }

        // get all files in s3 bucket less than 1 mb
        // get all files in s3 bucket less than 1 mb from s3 bucket
        $s3DeletableFiles = $this->s3Service->getFilesSmallerThan1MB($directory);
        foreach ($s3DeletableFiles as $file) {
            $this->info('Deleting file: ' . $file);
            // call s3:delete command
            Artisan::call('s3:delete', ['--file' => $file, '--directory' => $directory]);
        }


//        $message = [
//            'status' => 'success',
//            'Total songs to delete' => count($songs),
//            'Deleted songs count' => count($downloadedSongs),
//            'message' => 'File deleted successfully from ' . env('AWS_BUCKET') . '/' . $directory,
//        ];
//
//        $this->info(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        return 0;
    }
}
