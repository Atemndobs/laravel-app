<?php

namespace App\Console\Commands\FileWatcher;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MoveAudioUploadsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'move:audio';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move uploaded audio to audio folder';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Check files
        $audioFiles = glob('/var/www/html/storage/app/public/uploads/audio/*.mp3');
        $audioFiles = array_merge($audioFiles, glob('/var/www/html/storage/app/public/uploads/audio/*/*.mp3'));
        $audioFiles = array_merge($audioFiles, glob('/var/www/html/storage/app/public/uploads/audio/*/*/*.mp3'));
        $files = array_filter($audioFiles, 'is_file');
        $this->info('Found ' . count($audioFiles) . ' files');
        if (!$files) {
            $this->info('No files to move from uploads folder');
            Log::info('No files to move from uploads folder');
            return 0;
        }

        $this->moveFiles($audioFiles);
        return 0;
    }

    /**
     * @param bool|array $files
     * @return void
     */
    public function moveFiles(bool|array $files): void
    {
        foreach ($files as $file) {
            $fileName = basename($file);
            //$folder = basename(dirname($file));
            $folder = 'audio';
            $fileName = str_replace('.mp3', '', $fileName);
            $fileName = Str::slug($fileName, '_');
            $fileName = $fileName . '.mp3';
            $destination = "/var/www/html/storage/app/public/uploads/" .  $folder . '/' . $fileName;
//            if ($folder !== 'audio') {
//                $destination = "/var/www/html/storage/app/public/uploads/audio/" .  $folder . '/' . $fileName;
//            }
            if (!file_exists($destination)) {
                rename($file, $destination);
                Log::info('Moved ' . $file . ' to ' . $destination);
                $this->info('Moved ' . $file . ' to ' . $destination);
            }
        }
    }
}
