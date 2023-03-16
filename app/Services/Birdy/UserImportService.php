<?php

namespace App\Services\Birdy;

class UserImportService
{
    public function getUserData(int $currentPage, int $pageSize, string $fields)
    {
        $totalData = 362975;
        $pages = $totalData / $pageSize;

        if ($fields != 'null') {
            dump('fields : '.$fields);

            return $this->getFields($currentPage, $pageSize, $fields);
        }

        dump('current page : '.$currentPage."| size : $pageSize");

        return $this->prepareRequest($currentPage, $pageSize);
    }

    public function prepareRequest(int $currentPage = 1, int $pageSize = 300)
    {
        dump('PROD page : '.$currentPage."| size : $pageSize");

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://webshop.royalcanin.com/rest/V1/customers/search?searchCriteria%5BsortOrders%5D%5B0%5D%5Bdirection%5D=asc&searchCriteria%5BpageSize%5D=${pageSize}&searchCriteria%5BcurrentPage%5D=${currentPage}&fields=items%5Bid,group_id,store_id,extension_attributes%5Bcompany_attributes%5D%5D",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer h901on93fsi4ac485anrr2x66lnjbz37',
                'Cookie: PHPSESSID=nc6jius4n7nn8nqgg437jc8tpn',
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        $data = json_decode($response);
        $data->items['page'] = $currentPage;

        return $data->items;
    }

    public function requestDev(int $currentPage = 1, int $pageSize = 300)
    {
        dump('DEV REQUEST => current page : '.$currentPage."| size : $pageSize");
        $response = '';

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://admin.rc-dev.cgi-labs.de/rest/V1/customers/search?searchCriteria%5BsortOrders%5D%5B0%5D%5Bdirection%5D=asc&searchCriteria%5BpageSize%5D=${pageSize}&searchCriteria%5BcurrentPage%5D=${currentPage}&fields=items%5Bid,group_id,store_id,extension_attributes%5Bcompany_attributes%5D%5D",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer khtx9kxlmp73kmp2o69bahe1umd79cy3',
                'Cookie: PHPSESSID=p18jegcl5npbhbhqhqtlsjtq86',
            ],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);
        $data = json_decode($response);
        $data->items['page'] = $currentPage;

        return $data->items;
    }

    public function getFields(int $currentPage = 1, int $pageSize = 300, string $fields = 'id')
    {
        // "id,group_id,store_id,extension_attributes"
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://admin.rc-dev.cgi-labs.de/rest/V1/customers/search?searchCriteria%5BsortOrders%5D%5B0%5D%5Bdirection%5D=asc&searchCriteria%5BpageSize%5D=${pageSize}&searchCriteria%5BcurrentPage%5D=${currentPage}&fields=items%5B${fields}%5D",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer khtx9kxlmp73kmp2o69bahe1umd79cy3',
                'Cookie: PHPSESSID=dmaimnn2vep9mc02paij2tebe2',
            ],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        $data = json_decode($response);
        $data->items['page'] = $currentPage;

        return $data->items;
    }
}
