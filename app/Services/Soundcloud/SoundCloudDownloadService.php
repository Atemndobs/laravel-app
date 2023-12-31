<?php

namespace App\Services\Soundcloud;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Sarfraznawaz2005\ServerMonitor\Senders\Log;

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
        // save all filtered links in a file
        $filteredLinksFile = $path . "filtered_$fileName";
        file_put_contents($filteredLinksFile, implode("\n", $links));

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
                // Check if the link works
                // $filteredLinks = $this->getLinks($link);
                $filteredLinks[] = $link;
            }

        }

        return $filteredLinks;
    }

    /**
     * @param mixed $link
     * @return array
     */
    public function getLinks(mixed $link): array
    {
        $filteredLinks = [];
        $notWorkingLinks = [];
        \Illuminate\Support\Facades\Log::info("Checking link: $link");
        try {
            $get = Http::get($link);
            if ($get->successful()) {
                echo ("Link works: $link") . "\n";
                $filteredLinks[] = $link;
            } else {
                $notWorkingLinks[] = $link;
                dump("Link does not work: $link");
            }
        } catch (\Exception $e) {
            $notWorkingLinks[] = $link;
            dump("Link does not work: $link");
        }
        // save not working links in a file
        $notWorkingLinksFile = "not_working_links.txt";
        file_put_contents($notWorkingLinksFile, implode("\n", $notWorkingLinks));
        return $filteredLinks;
    }

}