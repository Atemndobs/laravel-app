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
        $files = glob(storage_path('app/public/uploads/audio/*'));
        if (!$files) {
            $this->info('No files to move from uploads folder');
            Log::info('No files to move from uploads folder');
            return 0;
        }

        $this->moveFiles($files);

        $audioFiles = glob(storage_path('app/public/audio/*.mp3'));
        if (!$audioFiles) {
            $this->info('No files to move from audio folder');
            Log::info('No files to move from audio folder');
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
            $fileName = str_replace('.mp3', '', $fileName);
            $fileName = Str::slug($fileName, '_');
            $fileName = $fileName . '.mp3';
            $destination = storage_path('app/public/uploads/audio/' . $fileName);
            if (!file_exists($destination)) {
                rename($file, $destination);
                Log::info('Moved ' . $file . ' to ' . $destination);
                $this->info('Moved ' . $file . ' to ' . $destination);
            }
        }
    }
}
