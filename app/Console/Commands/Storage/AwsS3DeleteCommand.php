<?php

namespace App\Console\Commands\Storage;

use Illuminate\Console\Command;
use App\Services\Storage\AwsS3Service;

class AwsS3DeleteCommand extends Command
{
    /**
     * Execute the console command.
     */
    protected $signature = 's3:delete {--f|file=} {--d|directory=music}';

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

        if (!$file) {
            $this->error('A file must be specified with -f option.');
            return 1;
        }

        $filePath = ("{$file}");

        $file_name = basename($filePath);
        $key = "{$directory}/{$file_name}";

        $result = $this->s3Service->deleteFile($directory, $file_name);

        $this->info(json_encode([
            'status' => 'success',
            's3_result' => $result,
            'message' => 'File deleted successfully from ' . env('AWS_BUCKET') . '/' . $key,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        return 0;
    }
}
