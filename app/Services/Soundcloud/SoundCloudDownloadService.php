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

        $file = array_filter($file, function ($line) {
            return !preg_match("/^https:\/\/soundcloud\.com\/tags/", $line)
                && !preg_match("/^https:\/\/soundcloud\.com\/you/", $line)
                && !preg_match("/^https:\/\/soundcloud\.com\/pages/", $line)
                && !preg_match("/^https:\/\/soundcloud\.com\/atmkng/", $line)
                && !preg_match("/^https:\/\/soundcloud\.com\/charts/", $line);
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
        // Prepare an incantation to hold the filtered song links
        $filteredLinks = array_filter($links, function($link) {
            // Check if the link is from soundcloud and not part of the excluded atmkng pattern
            if (strpos($link, 'https://soundcloud.com/') === 0 && !preg_match('#https://soundcloud.com/[^/]+/?$#', $link)) {
                // Count the slashes to ensure it's a song link (more than the base artist URL)
                $slashCount = substr_count($link, '/');
                // SoundCloud base URL has 3 slashes, artist URL will have 4, song links will have more
                return $slashCount > 4;
            }
            return false;
        });

        return array_values($filteredLinks);
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