<?php

namespace App\Console\Commands\Storage;

use App\Services\Storage\MinioService;
use Illuminate\Console\Command;

class MinioStatsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'minio:stats {--i|image=} {--a|audio=} {--d|dir=} {--f|all=false}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $image = $this->option('image');
        $audio = $this->option('audio');
        $dir = $this->option('dir');
        $all = $this->option('all');
        $minioService = new MinioService();
        if ($dir === null) {
            $dir = 'music';
        }
        if ($image ) {
            $this->info("Retrieving image stats from $image folder");
            $stats = $minioService->getAllImages();
            $count = count($stats);
            //$this->table(['image'], ['count' => $count]);
            $this->info("Total images: $count");
            return 0;
        }

        if ($audio !== null) {
            $this->info("Retrieving audio stats from $audio folder");
            $stats = $minioService->getAllAudios();
            $count = count($stats);
            // $this->table(['audio'], [$count]);
            $this->info("Total audios: $count");
            return 0;
        }
        if ($all !== false) {
            $this->info("Retrieving all stats from $dir folder");
            $audios = $minioService->getAllAudios();
            $images = $minioService->getAllImages();
            $count = count($audios) + count($images);
            $this->info("Total images: " . count($images));
            $this->info("Total audio: " . count($audios));
            $this->info("Total files: $count");
          //  $this->table(['files'], ['count' => $count]);
            return 0;
        }
        return 0;
    }
}
