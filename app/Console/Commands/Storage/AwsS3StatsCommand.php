<?php

namespace App\Console\Commands\Storage;

use Illuminate\Console\Command;
use App\Services\Storage\AwsS3Service;

class AwsS3StatsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 's3:stat {--d|dir=} {--a|all}';

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
        $all = $this->option('all');
        $options = ([
            'dir' => $dir,
            'all' => $all,
            'null' => $dir === null,
            'false' => $dir === 'false',
            'string' => $dir === 'null',
        ]);
        $this->warn(json_encode(['options' => $options], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        if ($dir !== null) {
            $files = $this->s3Service->getFiles($dir);
            $countFiles = count($files);
            $message = [
                $dir => $files,
                'stats' => [
                    'count' => $countFiles,
                    'directory' => $dir,
                    'bucket' => 'curators3',
                    'message' => "Found {$countFiles} files in the s3 bucket"
                ]
            ];
            $this->info(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            return;
        }
        $musicFiles = $this->s3Service->getFiles('music');
        $imagesFiles = $this->s3Service->getFiles('images');
        $backupFiles = $this->s3Service->getFiles('backups');
        $assetsFiles = $this->s3Service->getFiles('assets');

        $countMusicFiles = count($musicFiles);
        $countImagesFiles = count($imagesFiles);

        $message = [
            'music' => $countMusicFiles,
            'images' => $countImagesFiles,
            'backups' => count($backupFiles),
            'assets' => count($assetsFiles),
            'stats' => "Found {$countMusicFiles} music files and {$countImagesFiles} images files in the s3 bucket"
        ];
        $this->info(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

    }
}
