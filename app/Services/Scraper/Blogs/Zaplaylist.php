<?php

namespace App\Services\Scraper\Blogs;

// import all methods from MusicBlockScraper for ZaplayList
use App\Services\Scraper\Tools;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Zaplaylist
{
    use Tools;

    /**
     * @param  array  $searchTerms
     * @return array
     */
    public function getSongsFromZaplaylist(array $searchTerms): array
    {
        //https://zaplaylist.com/?s=Big+Flexa
        if (empty($searchTerms)) {
            return [];
        }
        ray($searchTerms);
        $searchOptions = $this->getSearchOptions($searchTerms);
        ray(['searchOptions' => $searchOptions]);

        if (count($searchOptions) === 1) {
            // if only title search option is set, get song links
            if (array_key_exists('title', $searchOptions)) {
                $songLinks = $this->getSongLinks($searchOptions['title']);
            } elseif (array_key_exists('artist', $searchOptions)) {
                // if only artist search option is set, get artist links
                $songLinks = $this->getArtistLinksFromZaplaylist($searchOptions['artist']);
            } else {
                $songLinks = $this->getSongLinks($searchOptions['artist']);
            }
            $songLinks = $this->filterDownloadUrls($songLinks);

            if (empty($songLinks)) {
                return [];
            }
        } else {
            $songLinks = $this->getSongLinksFromMultipleOptions($searchOptions);
        }

        return $songLinks;
    }

    /**
     * @param  string  $artist
     * @return array
     */
    private function getArtistLinksFromZaplaylist(string $artist): array
    {
        $songLinks = $this->getSongLinks($artist);

        return $this->filterDownloadUrls($songLinks);
    }

    /**
     * @param  array  $songLinks
     * @param  string  $title
     * @return array
     */
    private function filterSongLinksByTitle(array $songLinks, string $title): array
    {
        // replace + with - in title
        $title = str_replace('+', '-', $title);
        $title = Str::slug($title, '-');

        return array_filter($songLinks, function ($link) use ($title) {
            return str_contains($link, $title);
        });
    }

    /**
     * @param  mixed  $songFoundUrl
     * @return string
     */
    public function downloadZaplaylist(mixed $songFoundUrl): string
    {
        $res = $this->client->request('GET', $songFoundUrl);
        $downloadUrls = $res->filter('a')->each(function ($node) {
            return $node->attr('href').'';
        });
        $downloadUrls = array_unique($downloadUrls);
        $downloadUrls = $this->filterDownloadUrls($downloadUrls, '.mp3');
        $downloadUrl = Arr::first($downloadUrls);
        $fileName = explode('/', $downloadUrl);
        $fileName = Arr::last($fileName);
        $this->download($fileName, $downloadUrl);

        return $downloadUrl;
    }

    /**
     * @param  array  $searchTerms
     * @return array
     */
    public function getSearchOptions(array $searchTerms): array
    {
        $searchOptions = [];
        $searchUrl = 'https://zaplaylist.com/?';
        foreach ($searchTerms as $search => $term) {
            $term = Str::slug($term, '+');
            $artist = $search === 'artist' ? $searchUrl."s=$term" : null;
            if ($artist !== null) {
                $searchOptions['artist'] = $artist;
            }
            $title = $search === 'title' ? $searchUrl."s=$term" : null;
            if ($title !== null) {
                $searchOptions['title'] = $title;
            }
            $mixtape = $search === 'mixtape' ? $searchUrl."s=$term" : null;
            if ($mixtape !== null) {
                $searchOptions['mixtape'] = $mixtape;
            }
        }

        return $searchOptions;
    }

    /**
     * @param  array  $searchOptions
     * @return array
     */
    public function getSongLinksFromMultipleOptions(array $searchOptions): array
    {
        ray()->clearAll();
        dump('CONTAINS MORE THAN ONE SEARCH OPTION');
        $songLinks = $this->getSongLinks($searchOptions['title']);
        $songLinks = $this->filterDownloadUrls($songLinks);
        if (empty($songLinks)) {
            $songLinks = $this->getArtistLinksFromZaplaylist($searchOptions['artist']);
        }
        if (count($songLinks) > 1) {
            $songLinks = $this->filterSongLinksByTitle($songLinks, $searchOptions['title']);
        }

        return [Arr::first($songLinks)];
    }

    /**
     * @param  array  $songLinks
     * @return array
     */
    public function downloadMultipleSongs(array $songLinks): array
    {
        $res = [];
        foreach ($songLinks as $songLink) {
            dump("downloading ...  $songLink");
            $downloadLink = $this->downloadZaplaylist($songLink);
            $res[] = $downloadLink;
        }

        return $res;
    }
}
