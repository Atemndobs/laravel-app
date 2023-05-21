<?php

namespace App\Services\Scraper;

use Goutte\Client;

trait Tools
{
    public Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * @param $outFileName
     * @param $url
     * @return mixed|void
     */
    public function download($outFileName, $url)
    {
        if (is_file($url)) {
            copy($url, $outFileName);
        } else {
            $options = [
                CURLOPT_FILE => fopen($outFileName, 'w'),
                CURLOPT_TIMEOUT => 28800, // set this to 8 hours so we dont timeout on big files
                CURLOPT_URL => $url,
            ];

            $ch = curl_init();
            curl_setopt_array($ch, $options);
            curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return $httpcode;
        }
    }

    /**
     * @param  string  $uri
     * @return array
     */
    public function getSongLinks(string $uri): array
    {
        $res = $this->client->request('GET', $uri);
        $songLinks = $res->filter('a')->each(function ($node) {
            return $node->attr('href').'';
        });

        return array_unique($songLinks);
    }

    /**
     * @param  array  $songLinks
     * @param  string  $filter
     * @return array
     */
    public function filterDownloadUrls(array $songLinks, string $filter = 'mp3/'): array
    {
        // filter links containing $title and ends with mp3
        return array_filter($songLinks, function ($link) use ($filter) {
            //dump($link);
            return str_ends_with($link, $filter);
        });
    }
}
