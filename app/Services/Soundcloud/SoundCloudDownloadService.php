<?php

namespace App\Services\Soundcloud;

use Illuminate\Support\Str;

class SoundCloudDownloadService
{
    public function prepareSongLinksFromFile(string $fileName="soundcloud_likes.txt", string $path=""): array
    {
        $file = $path . $fileName;
        $file = file_get_contents($file);
        $file = explode("\n", $file);
        $file = array_filter($file);
        $file = array_unique($file);
        $file = array_values($file);
        $initialCount = count($file);
        // remove all lines starting with https://soundcloud.com/you
        $file = array_filter($file, function ($line) {
            return !Str::startsWith($line, 'https://soundcloud.com/you');
        });
        $links = $this->filterSoundCloudSongLinks($file);
        $filteredCount = count($links);
        return [
            'links' => $links,
            'initialCount' => $initialCount,
            'filteredCount' => $filteredCount,
        ];

    }

    function filterSoundCloudSongLinks($links) {
        $filteredLinks = [];

        foreach ($links as $link) {
            // Check if the link is a song link and not a profile or comments link
            if (preg_match("/^https:\/\/soundcloud\.com\/[\w-]+\/[\w-]+$/", $link)) {
                $filteredLinks[] = $link;
            }
        }

        return $filteredLinks;
    }

}