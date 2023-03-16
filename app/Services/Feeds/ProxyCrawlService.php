<?php

namespace App\Services\Feeds;

use Illuminate\Support\Facades\Http;

class ProxyCrawlService
{
    private const FULL_URL = 'https://api.proxycrawl.com/scraper?token=GRJwptEfJNjxvj0bfaKqtQ&url=';

    private AbstractApi $abstractApi;

    public function __construct()
    {
        $this->abstractApi = new AbstractApi();
    }

    public function scrapeSite($url = 'https%3A%2F%2Fwww.amazon.com%2Fdp%2FB00JITDVD2')
    {
        $url = urlencode($url);
        $getData = Http::get(self::FULL_URL.$url);

        return $getData->json();
    }

    public function abstractApi($base_url)
    {
        return $this->abstractApi->abstractApi($base_url);
    }
}
