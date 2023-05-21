<?php

namespace App\Services;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExportService implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        //   // id,group_id,store_id,extension_attributes
        return [
            'ID',
            'group_id',
            'store_id',
            'company_id',
        ];
    }
}
