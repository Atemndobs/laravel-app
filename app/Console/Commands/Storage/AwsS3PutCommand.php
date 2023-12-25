<?php

namespace App\Console\Commands\Storage;

use Illuminate\Console\Command;
use App\Services\Storage\AwsS3Service;

class AwsS3PutCommand extends Command
{
    /**
     * Execute the console command.
     */
    protected $signature = 's3:put {--f|file=} {--d|directory=music}';

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
        if (!file_exists($filePath)) {
            $this->error("File {$filePath} does not exist.");
            return 1;
        }

        $file_name = basename($filePath);
        $key = "{$directory}/{$file_name}";

        $result = $this->s3Service->uploadFile($filePath, env('AWS_BUCKET'), $key);
        $message = [
            'status' => 'success',
            's3_result' => $result,
            'message' => 'File uploaded successfully to ' . env('AWS_BUCKET') . '/' . $key,
        ];

        $this->info(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        return 0;
    }
}
