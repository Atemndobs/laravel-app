<?php

// Import User From Magento API

namespace App\Console\Commands\Db;

use App\Services\Birdy\UserImportService;
use App\Services\MultipleSheeExport;
use App\Services\UsersExportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ImportUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:user {currentPage?} {pageSize?} {--p|page=0} {--s|size=20} {--f|field=null} {--l|limit=10}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $path = Storage::url('data');

        $pageSize = $this->argument('pageSize');
        $currentPage = $this->argument('currentPage');

        $size = $this->option('size');
        $page = $this->option('page');
        $field = $this->option('field');
        $limit = $this->option('limit');

        $totalData = 362975;
        $totalDevData = 299357;
        $pages = 1;

        $sizeCalc = $pageSize ?? $size;

        $pages = $totalDevData / $sizeCalc;

        $import = new UserImportService();

        dump([
            'totals_user_Count' => $totalDevData,
            'totals_page_Count' => $pages,
            'current_page' => $currentPage ?? $page,
            'page_size' => $pageSize ?? $size,
            'fields' => $field,
            'limit' => $limit,

        ]);

        $importedData = [];

        if ($currentPage != 0 || $page != 0) {
            $i = $currentPage ?? $page;
            $importedData = $import->getUserData($i, $size, $field);
            dd($importedData);
        }

        if ($limit === 'max') {
            for ($currentPage = 1; $currentPage <= $pages; $currentPage++) {
                $this->getImportedDataPerPage($currentPage, $import, $size, $field, $importedData);
                sleep(5);
                dump('Sleeping 5 sec');
            }
        }

        dd('DONE');

        //  $importedData = $this->getData($pages, $limit, $import, $size, $field, $importedData);
    }

    /**
     * @param  float|int  $pages
     * @param  mixed  $limit
     * @param  UserImportService  $import
     * @param  int  $size
     * @param  string  $field
     * @param  array  $importedData
     * @return false|string
     */
    public function getData(float|int $pages, int $limit, UserImportService $import, int $size, string $field, array $importedData)
    {
        for ($currentPage = 1; $currentPage <= $pages; $currentPage++) {
            if ($currentPage === $limit) {
                break;
            }

            $importedData[] = $import->getUserData($currentPage, $size, $field);
        }
        $data = json_encode($importedData);
        Storage::put("data/$limit.json", $data);

        return $data;
    }

    /**
     * @param  int  $currentPage
     * @param  UserImportService  $import
     * @param  int  $size
     * @param  string  $field
     * @param  array  $importedData
     * @return array
     */
    public function getImportedDataPerPage(int $currentPage, UserImportService $import, int $size, string $field, array $importedData): array
    {
        dump(['currenct page : ' => $currentPage]);
        $importedData[] = $import->getUserData($currentPage, $size, $field);
        Storage::append('data/data.json', json_encode($importedData));
        $finish = [];
//
//        if ($currentPage === 3){
//            die($currentPage);
//        }

        // id,group_id,store_id,extension_attributes
        foreach ($importedData[0] as $key => $datum) {
            if ($key === 'page') {
                continue;
            }
            $finish[] = [
                $datum->id ?? null,
                $datum->group_id ?? null,
                $datum->store_id ?? null,
                $datum->extension_attributes->company_attributes->company_id ?? null,
            ];
        }
        Excel::download((new UsersExportService($finish)), 'users.xlsx');
        Excel::download((new UsersExportService($finish)), 'users.csv');

        //   Excel::download((new MultipleSheeExport($finish)), 'users.xlsx');

        return $importedData;
    }
}
