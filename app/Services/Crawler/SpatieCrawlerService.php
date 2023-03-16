<?php

namespace App\Services\Crawler;

use Goutte\Client as Goutte;
use GuzzleHttp\Client;
use Spatie\Crawler\Crawler;

class SpatieCrawlerService
{
    public Client $client;

    public Goutte $goutte;

    /**
     * @param  Client  $client
     * @param  Goutte  $goutte
     */
    public function __construct(Client $client, Goutte $goutte)
    {
        $this->client = $client;
        $this->goutte = $goutte;
    }

    public function crawlUrl($url)
    {
        Crawler::create()
            ->setCrawlObserver(new CrawlObserver())
                ->startCrawling($url);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPage($url)
    {
        // $response = $this->client->get( $url);
        //$content = $response->getBody()->getContents();

        $crawler = $this->goutte->request('GET', 'https://laravelexamples.com/');

        $site = $crawler;

        //        /html/body/div/main/div/div[1]/a[2]/div/div/p

        return [
            'site_text' => $site->filterXPath('')->text(),
        ];
    }
}
