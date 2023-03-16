<?php

namespace App\Console\Commands\Song;

use App\Models\Song;

trait Tools
{
    /**
     * @param Song $song
     * @return bool
     */
    public function checkAudioFile(Song $song)
    {
        $path = $song->path;
        $path = str_replace('http://mage.tech:8899/storage/', '', $path);
        $path = storage_path('app/public/' .  $path) ;
        if (file_exists($path)) {
            return true;
        }
        return false;
    }
}
