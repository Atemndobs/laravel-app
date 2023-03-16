<?php

namespace App\Services\Scraper\Blogs;

use App\Services\Scraper\Tools;
use League\CommonMark\Extension\CommonMark\Node\Block\ThematicBreak;

class Clacified
{
    public string $baseUrl = 'https://clacified.com';

    use Tools;


    public function getTop20()
    {
        $top20 = [];
        $link = $this->baseUrl.'/entertainment/16/top-trending-naija-songs-2022';

        $getSongLinks = $this->getSongLinks($link);
        
    }
}
