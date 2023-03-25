<?php

namespace App\Console\Commands\Storage;

use App\Services\Storage\MinioService;
use Illuminate\Console\Command;

class MinioGetFileCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'minio:get {--f|file=} {--d|dir=} {--a|all=false}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'retrieve files from Minio S3';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $file = $this->option('file');
        $dir = $this->option('dir');
        $all = $this->option('all');
        $filename = basename($file);
        if (!$file && !$dir) {
            $this->error('No file or directory specified');
            return;
        }

        $minioService = new MinioService();
        $dir = $dir ?? 'music';

        if ($file !== null){
            dump([
                "GET FILE COMMAND",
                'AWS_URL' => env('AWS_URL'),
                'url' => config('filesystems.disks.s3.url'),
                'path' => config('filesystems.disks.s3.path'),
                'bucket' => config('filesystems.disks.s3.bucket'),
                'client' =>
                    [
                        'key' => config('filesystems.disks.s3.client.key'),
                        'secret' => config('filesystems.disks.s3.client.secret'),
                        'region' => config('filesystems.disks.s3.client.region'),
                        'version' => config('filesystems.disks.s3.client.version'),
                    ],
                'options' =>
                    [
                        'ACL' => config('filesystems.disks.s3.options.ACL'),
                        'CacheControl' => config('filesystems.disks.s3.options.CacheControl'),
                        'ContentType' => config('filesystems.disks.s3.options.ContentType'),
                    ],
            ]);

            $this->line("<fg=yellow>Retrieving </>" . "<fg=magenta> $file</>" . "<fg=yellow> from </>" . "<fg=magenta> $dir</>");
            $this->newLine(1 , "Retrieving $file from $dir");
            $file = $minioService->getAudio($filename, $dir);
            $this->info($file);
            return;
        }
        if ($dir === 'music') {
            if ($all !== false) {
                $this->info("Retrieving all files from $dir");
                try {
                    $files = $minioService->getAllAudios($dir);
                    $this->getFilesTable($files, $dir);
                    return;
                }catch (\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            $this->info("Retrieving files from $dir");
            try {
                $file = $minioService->getAudio($filename, $dir);
                $this->info($file);
                return;
            }catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        } else {
            if ($all !== false) {
                $this->info("Retrieving all files from $dir");
                try {
                    $files = $minioService->getAllImages($dir);
                    $this->getFilesTable($files, $dir);
                    return;
                }catch (\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            $this->info("Retrieving $file from $dir");
            try {
                $file = $minioService->getImage($filename, $dir);
                $this->info($file);
                return;
            }catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        }


    }

    /**
     * @param array $files
     * @param string $dir
     */
    public function getFilesTable(array $files, string $dir)
    {
        $results = [];
        foreach ($files as $file) {
            $results[] = [
                $file
            ];
        }

        // output files in table
        $this->table([$dir], $results);
    }
}
