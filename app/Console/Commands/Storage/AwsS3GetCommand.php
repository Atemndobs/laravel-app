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
    protected $signature = 's3:get  ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $musicFiles = $this->s3Service->getFiles('music');
        $imagesFiles = $this->s3Service->getFiles('images');

        $countMusicFiles = count($musicFiles);
        $countImagesFiles = count($imagesFiles);

        $message = [
            'music' => $countMusicFiles,
            'images' => $countImagesFiles,
            'stats' => "Found {$countMusicFiles} music files and {$countImagesFiles} images files in the s3 bucket"
        ];
        $this->info(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));


    }
}
