<?php

namespace App\Console\Commands\Storage;

use Illuminate\Console\Command;
use App\Services\Storage\AwsS3Service;

class AwsS3PutManyCommand extends Command
{
    /**
     * Execute the console command.
     */
    protected $signature = 's3:multi-put {--s|source=} {--d|directory=music}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Uploads multiple files to S3';



    private AwsS3Service $s3Service;

    public function __construct(AwsS3Service $s3Service)
    {
        parent::__construct();
        $this->s3Service = $s3Service;
    }

    public function handle()
    {
        $source = $this->option('source');
        $directory = $this->option('directory');
        $this->warn(json_encode($this->options(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $sourcePath = ("{$source}");
        if (!str_contains($source, '/')) {
            $sourcePath = "/var/www/html/storage/app/public/uploads/{$source}";
        }
        if (!$source) {
            $this->error('A Sour must be specified with -s option. example audio or images');
            return 1;
        }


        if (!file_exists($sourcePath)) {
            $this->error("Source {$sourcePath} does not exist.");
            return 1;
        }

        $files = glob($sourcePath . '/*.mp3');
        // for each file, upload it to s3
        $count = count($files);
        $this->info("Found $count files to upload");
        $bar = $this->output->createProgressBar($count);
        $bar->start();
        $processed = [];
        foreach ($files as $file) {
            $file_name = basename($file);
            $key = "{$directory}/{$file_name}";
            try {
                $result = $this->s3Service->uploadFile($file, env('AWS_BUCKET'), $key);
                $processed[] = $file;
                // delete file
                $delete = unlink($file);
                if (!$delete) {
                    $this->error("Could not delete file $file");
                }
            }catch (\Exception $e){
                $this->error($e->getMessage());
                continue;
            }
            $bar->advance();
            $message = [
                'status' => 'success',
                's3_result' => $result,
                'deleted? ' => $delete,
                'message' => 'File uploaded successfully to ' . env('AWS_BUCKET') . '/' . $key,
            ];
            $this->line("<fg=bright-magenta>" . json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES). "</>");
        }

        $message = [
            'status' => 'success',
            'uploaded_files' => count($processed),
            'message' => 'Files uploaded successfully to ' . env('AWS_BUCKET') . '/' . $directory,
        ];

        $this->info(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        return 0;
    }
}
