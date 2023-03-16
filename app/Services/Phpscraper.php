<?php

namespace App\Services;

class Phpscraper
{
    public function scrape(string $url, array $options = [])
    {
        $web = new \Spekulatius\PHPScraper\PHPScraper;
        if (!$url) {
            throw new \Exception('please provide a url');
        }
        $web->go($url);

        return [
            "Web title" => $web->images ,

        ];
    }
}

