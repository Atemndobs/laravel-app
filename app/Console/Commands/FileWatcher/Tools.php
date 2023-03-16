<?php

namespace App\Console\Commands\FileWatcher;

use Illuminate\Support\Str;
use Spatie\Watcher\Watch;

trait Tools
{
    /**
     * @param string $dir
     * @param string $destination
     * @return void
     */
    public function watchFolder(string $dir, string $destination): void
    {
        try {
            Watch::path($dir)
                ->onFileCreated(function (string $path) use ($destination) {
                    sleep(2);
                    if (str_contains($path, '.mp3') || str_contains($path, '.wav')) {
                        $fileName = basename($path);
                        $ext = pathinfo($path, PATHINFO_EXTENSION);
                        $fileName = substr($fileName, 0, -4);
                        $fileName = Str::slug($fileName, '_');
                        $fileName = $fileName . '.' . $ext;
                        $this->call('song:import');
                        sleep(2);
                        $audioPath = storage_path("app/public/music/$destination/".$fileName);
                        rename($path, $audioPath);
                        $this->line("<fg=magenta>Song Imported | $path</>");
                    }
                })
                ->onFileUpdated(function (string $path) {
                    if (str_contains($path, '.mp3')) {
                        $this->line("<fg=blue>Song Path Has been Updated | $path</>");
                    }
                })
                ->start();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
