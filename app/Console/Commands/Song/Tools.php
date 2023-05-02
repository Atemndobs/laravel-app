<?php

namespace App\Console\Commands\Song;

use App\Models\Song;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

trait Tools
{
    /**
     * @param Song $song
     * @return bool
     */
    public function checkAudioFile(Song $song)
    {
        $path = $song->path;

        // Check if song exit in cloud storage
        try {
            $exists = Http::get($path)->ok();
            if ($exists) {
                return true;
            }
        } catch (\Exception $e) {
            return false;
        }
//        $path = str_replace('http://mage.tech:8899/storage/', '', $path);
//        $path = storage_path('app/public/' .  $path) ;
//        if (file_exists($path)) {
//            return true;
//        }
        return false;
    }
}
