<?php

namespace App\Console\Commands\Song;

use App\Services\Storage\AwsS3Service;
use App\Services\Storage\MinioService;
use App\Services\UploadService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Statamic\Support\Str;

class SongImportUnsortedS3Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:s3';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import unsorted songs from S3';

    /**
     * Execute the console command.
     * @throws \Exception
     */
    public function handle()
    {
        $minioService = new AwsS3Service();
        $files = $minioService->getUnsortedSongs();
        $bar = $this->output->createProgressBar(count($files));
        $uploadService = new UploadService();
        $uploadedSongs = [];


        dd([
            'files' => $files,
            'count' => count($files),
            'uploadedSongs' => $uploadedSongs
        ]);
        foreach ($files as $file) {
            $filepath = $minioService->downloadSong($file);
            $uploadedSong = $uploadService->uploadSong($filepath);
            $uploadedSongs[] = $uploadedSong;
            Log::info(json_encode(
                [
                    'uploaded' => $uploadedSong->path,
                    'slug' => $uploadedSong->slug
                ], JSON_UNESCAPED_SLASHES |  JSON_PRETTY_PRINT) );
            $bar->advance();
        }
        Log::info('Uploaded ' . count($uploadedSongs) . ' songs');
        $bar->finish();
        return 0;
    }
}
