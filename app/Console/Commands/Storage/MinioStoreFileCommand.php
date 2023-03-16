<?php

namespace App\Console\Commands\Storage;

use App\Services\Storage\MinioService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Monolog\Handler\IFTTTHandler;

class MinioStoreFileCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'minio:put {--f|file=} {--d|dir=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store files from Minio S3';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $content = $this->option('file');
        $dir = $this->option('dir');

        if (!$content && !$dir) {
            $this->error('No file or directory specified');
            return;
        }

        $minioService = new MinioService();
        if (is_dir($content)) {
            $files = glob($content . '/*');
            foreach ($files as $file) {

                if ($file === '.' || $file === '..' || is_dir($file) || $file === '.mp3') {
                    continue;
                }

                if ( pathinfo($file, PATHINFO_EXTENSION) === 'mp3' ||
                    pathinfo($file, PATHINFO_EXTENSION) === 'jpeg' ||
                    pathinfo($file, PATHINFO_EXTENSION) === 'jpeg'
                ) {
                    try {
                        $filename = basename($file);
                        $this->line("<fg=blue>Uploading $filename</>");
                        $minioService->putObject($file, $dir);
                        $this->info("File $filename uploaded to Minio S3");
                    } catch (\Exception $e) {
                        $this->error($e->getMessage());
                    }
                }

            }
        } else {
            $this->info("Uploading $content");
            $minioService->putObject($content, $dir);
            $filename = basename($content);
            $this->info("File $filename uploaded to Minio S3");
        }







    }
}
