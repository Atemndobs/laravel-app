<?php

namespace App\Console\Commands\Storage;

use Illuminate\Console\Command;
use App\Services\Storage\AwsS3Service;

class AwsS3GetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 's3:get {--d|dir=} {--f|file=} {--a|all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get count of files from s3 bucket folders : music, images, backups, assets. 
    option --dir to get files from specific folder';

    private AwsS3Service $s3Service;

    public function __construct(AwsS3Service $s3Service)
    {
        parent::__construct();
        $this->s3Service = $s3Service;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // get all files in music folder and images folder from s3 bucket and count them
        $dir = $this->option('dir');
        $file = $this->option('file');
        $all = $this->option('all');
        $options = ([
            'dir' => $dir,
            'file' => $file,
            'all' => $all,
        ]);

        // if file is not provided, return error and exit
        if ($file === null) {
            $this->error('Please provide a file name');
            return;
        }
        $this->warn(json_encode(['options' => $options], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        if ($dir !== null) {
            try {
                $object = $this->s3Service->getObject($dir, $file);
                $this->downloadBackup($object, $dir);
            } catch (\Exception $e) {
                $this->line("<fg=red>{$e->getMessage()}</>");
                return;
            }
            $message = [
                'stats' => [
                    'directory' => $dir,
                    'bucket' => 'curators3',
                    's3_url' => $object,
                ]
            ];
            $this->info(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            return;
        }
        try {
            $object = $this->s3Service->getObject($dir, $file);
        } catch (\Exception $e) {;
            $this->line("<fg=red>{$e->getMessage()}</>");
            return;
        }

        $message = [
            'directory' => $dir,
            'bucket' => 'curators3',
            's3_url' => $object,
        ];
        $this->info(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    private function downloadBackup(string $object, string $dir = 'backups'): void
    {
        $fileName = basename($object);
        $path = storage_path("app/$dir/" . $fileName);
        $this->downloadFile($object, $path, $dir);
    }

    public function downloadObject(string $object, string $dir = 'music'): void
    {
        $dir = $dir === 'music' ? 'audio' : $dir;
        $fileName = basename($object);
        $path = storage_path("app/public/uploads/$dir/" . $fileName);
        $this->downloadFile($object, $path, $dir);
    }

    /**
     * @param string $object
     * @param string $path
     * @param string $dir
     * @return void
     */
    public function downloadFile(string $object, string $path, string $dir): void
    {
        $this->info('Downloading file...');
        $file = file_get_contents($object);
        $this->info('File downloaded successfully');
        $this->info('Writing file to disk...');

        file_put_contents($path, $file);
        $this->info('File written to disk successfully');
        $this->info('File path: ' . $path);
        $message = [
            'directory' => $dir,
            'file_name' => $object,
            'file_path' => $path,
        ];
        $this->line("<fg=bright-magenta>" . json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "</>");
    }

}
