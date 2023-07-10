<?php

namespace App\Console\Commands\Storage;

use App\Services\Storage\MinioService;
use Illuminate\Console\Command;

class MinioGetUrlCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'minio:url {--f|file=} {--d|dir=} {--b|bucket=laboom}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Url for s3 bucket or file in s3 bucket -d for directory -f for file -a for all - b for bucket';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->option('file');
        $dir = $this->option('dir');
        $bucket = $this->option('bucket');
        $minioService = new MinioService();
        if ($dir === null) {
            $dir = 'music';
        }
        if ($file !== null) {
            $this->info("Retrieving url for $file file in $dir folder");

            return 0;
        }
        if ($bucket !== null) {
            $this->info("Retrieving url for $bucket bucket");
            $url = $minioService->getBucketUrl($bucket, $dir);
            $this->info($url);
            $this->output->writeln($url);
            return 0;
        }
        $this->info("Retrieving url for $dir folder");

        return 0;

    }
}
