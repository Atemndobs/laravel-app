<?php

namespace App\Services\Feeds;

use Illuminate\Support\Facades\Http;

class AbstractApi
{
    private const API_TOKEN = 'cc7018f141784b13948ce98e5a456a44';

    private const BASE_URL = 'https://scrape.abstractapi.com/v1/';

    private $base;

    public function __construct()
    {
        $this->base = self::BASE_URL.'?api_key='.self::API_TOKEN.'=';
    }

    /**
     * @param  string  $base_url
     * @return string
     */
    public function abstractApi(string $base_url)
    {
        // Initialize cURL.
        $ch = curl_init();

        // Set the URL that you want to GET by using the CURLOPT_URL option.
        curl_setopt($ch, CURLOPT_URL, 'https://scrape.abstractapi.com/v1/?api_key=cc7018f141784b13948ce98e5a456a44&url='.$base_url);

        // Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        // Execute the request.
        $data = curl_exec($ch);

        // Close the cURL handle.
        curl_close($ch);

        // Print the data out onto the page.
        return $data;
    }

    public function test(string $base_url)
    {
        //$url = $this->base . $base_url;
        // https://scrape.abstractapi.com/v1/?api_key=cc7018f141784b13948ce98e5a456a44&url=https://news.ycombinator.com;
        $url = 'https://scrape.abstractapi.com/v1/?api_key=cc7018f141784b13948ce98e5a456a44&url=https://laravelexamples.com/example/laravel-io/laravel-socialite';

        $getData = Http::get($url);

        return $getData->body();
    }
}
