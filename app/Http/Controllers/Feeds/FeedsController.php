<?php

namespace App\Http\Controllers\Feeds;

use App\Http\Controllers\Controller;
use App\Services\Crawler\SpatieCrawlerService;
use App\Services\Feeds\ProxyCrawlService;
use App\Services\Feeds\StrapiService;
use Dbfx\LaravelStrapi\LaravelStrapi;
use DOMDocument;
use DOMXPath;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Prettus\Repository\Exceptions\RepositoryException;
use function request;
use function response;

/**
 * Class FeedsController.
 */
class FeedsController extends Controller
{
    protected SpatieCrawlerService $spatieCrawlerService;

    private ProxyCrawlService $proxyCrawlService;

    private StrapiService $strapiService;

    public Request $request;

    /**
     * @param  SpatieCrawlerService  $spatieCrawlerService
     * @param  ProxyCrawlService  $proxyCrawlService
     * @param  StrapiService  $strapiService
     * @param  Request  $request
     */
    public function __construct(
        SpatieCrawlerService $spatieCrawlerService,
        ProxyCrawlService $proxyCrawlService,
        StrapiService $strapiService,
        Request $request
    ) {
        $this->spatieCrawlerService = $spatieCrawlerService;
        $this->proxyCrawlService = $proxyCrawlService;
        $this->strapiService = $strapiService;
        $this->request = $request;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     *
     * @throws RepositoryException
     */
    public function index()
    {
        $getByFiled = $this->getExtractById();

        return response()->json($getByFiled);
    }

    public function sort()
    {
        $httpClient = new \GuzzleHttp\Client();

        // 172.17.0.1:1337/api
        //$response = $httpClient->get('http://172.17.0.1:8899/api/feeds/');
//        $url = 'https://laravel-news.com/websocket-handbook';
//        $response = $httpClient->get($url);
//
//        $htmlString = (string) $response->getBody();
//        $this->strapiService->saveExtractions('extracts', $htmlString, $url);

        $htmlString = $this->getExtractById();

        dd($htmlString);

        // HTML is often wonky, this suppresses a lot of warnings
        libxml_use_internal_errors(true);

        $doc = new DOMDocument();
        $doc->loadHTML($htmlString);

        $xpath = new DOMXPath($doc);

        return $xpath;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     *
     * @throws RepositoryException
     */
    public function cloud()
    {
        $url = 'https://laravelexamples.com/';
        $url2 = 'https://www.google.com/search?q=magento+security+releases';
        $url3 = 'https://helpx.adobe.com/security/security-bulletin.html';

        $data = $this->proxyCrawlService->abstractApi($url);

        $strapi = new LaravelStrapi();
        $extracted = $strapi->collection('books');

        return response()->json([
            'search_query' => ($url3),
            'data' => $extracted,
        ]);
    }

    public function samleCreate($data)
    {
        return  [
            'data' => [
                'source' => $data['source'],
                'data' => $data['data'],
                'createdAt' => '2022-03-07T22:04:48.442Z',
                'updatedAt' => '2022-03-07T22:04:48.442Z',
                'publishedAt' => '2022-03-07T22:04:48.442Z',
                'createdBy' => 'string or id',
                'updatedBy' => 'string or id',
            ],
        ];
    }

    public function new()
    {
        $url = $this->request->link;

        $strapi = new LaravelStrapi();

        $data = [];

        // $url = 'https://notjustok.com/category/mp3-songs/';
        // $url = 'https://fakaza.com/tag/amapiano/';
        // $data = $this->proxyCrawlService->abstractApi($url);

        //  $data = $this->proxyCrawlService->scrapeSite($url);

        return $this->strapiService->saveExtractions('extracts', $data, $url);
    }

    /**
     * @return string
     */
    public function getExtractById(): string
    {
        $id = $this->request->id > 0 ? $this->request->id : 10;
        $getByFiled = $this->strapiService->getByField('extracts', $id, 'raw_data');

        $response = $this->strapiService->getById('extracts', $id);

        return $getByFiled;
    }

    public function classify()
    {
        echo 'HERE';
    }
}
