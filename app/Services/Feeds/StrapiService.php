<?php

namespace App\Services\Feeds;

use Dbfx\LaravelStrapi\LaravelStrapi;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class StrapiService
{
    public function saveExtractions(string $type, array|string $data, $url = '')
    {
        $strapiUrl = config('strapi.url');
        $resource = $strapiUrl."/$type";

        dd($resource);

        $body = $data;
        $source = $url;

        if (is_array($data)) {
            $source = $data['url'] ?? $url;
            $body = $data['url'] ? $data['body'] : $data;
        }

        $payload = [
            'source' => $source,
            'data' => json_encode($body),
            'raw_data' => $body,
            'edited_data' => $body,
        ];

        $request = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post($resource, [
            'data' => $payload,
        ]);

        return $request->json();
    }

    /**
     * @param  string  $type
     * @param  int  $id
     * @return mixed
     */
    public function getById(string $type, int $id): array
    {
        $strapi = new LaravelStrapi();
        $extracted = $strapi->collection($type);

        $datas = $extracted['data'];

        $response = [];
        foreach ($datas as $data) {
            if ($data['id'] === $id) {
                $response = $data['attributes'];
            }
        }

        return $response;
    }

    /**
     * @param  string  $type
     * @param  int  $id
     * @param  string  $field
     * @return string
     */
    public function getByField(string $type, int $id, string $field): string
    {
        if (Arr::exists($this->getById($type, $id), $field)) {
            return $this->getById($type, $id)[$field];
        }

        return "The field $field does not exit";
    }
}
