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
                    $fileName = basename($path);
                    sleep(2);
                    if (!is_file($path)) {
                        $folder = basename(dirname($path));
                        $audioPath = storage_path('app/public/audio/' . $folder . '/' . $fileName);
                    }
                    if (str_contains($path, '.mp3') || str_contains($path, '.wav')) {

                        $ext = pathinfo($path, PATHINFO_EXTENSION);
                        $fileName = substr($fileName, 0, -4);
                        $fileName = Str::slug($fileName, '_');
                        $fileName = $fileName . '.' . $ext;
                        $audioPath = "/var/www/html/storage/app/public/audio/$destination/".$fileName;

                        dd([
                            'path' => $path,
                            'audioPath' => $audioPath,
                            'fileName' => $fileName,
                            'ext' => $ext,
                        ]);
                       // rename($path, $audioPath);
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
