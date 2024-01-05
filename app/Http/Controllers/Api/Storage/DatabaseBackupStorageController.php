<?php

namespace App\Http\Controllers\Api\Storage;

use App\Http\Controllers\Controller;
use App\Services\Storage\AwsS3Service;
use App\Services\Storage\MinioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class DatabaseBackupStorageController extends Controller
{
    public function downloadBackup()
    {
        // download the backup file from storage/app/backups/latest/db-dumps/mysql-mage.sql
        $file = storage_path('app/backups/latest/db-dumps/mysql-mage.sql');
        // Add date to filename
        $date = date('Y-m-d');
        $filename = "backup-$date.sql";
        $headers = [
            'Content-Type' => 'application/sql',
        ];
        return response()->download($file, $filename, $headers);
    }

    /**
     * @throws \Exception
     */
    public function storeBackup(): JsonResponse
    {
        // store file to minio storage
        // use Date and time as filename
        $date = date('Y-m-d');
        $time = date('H:i:s');
        $filename = "backup-$date-$time.sql";
        $file = storage_path('app/backups/latest/db-dumps/mysql-mage.sql');


        $s3Service = new AwsS3Service();
        $fileLink = $s3Service->putObjectWithFileName($file, "backups", $filename);
        return response()->json([
            'message' => 'SUCCESS',
            'file_link' => $fileLink
        ]);
    }

    public function uploadBackup()
    {
        // Upload the backup file to storage/app/backups/latest/db-dumps/mysql-mage.sql
        $filename = "mysql-mage.sql";
        $file = request()->file('file');
        // check file type is sql

        try {
            $file->storeAs('backups/latest/db-dumps', $filename);
            $response = 'SUCCESS';
        }catch (\Exception $exception){
            $response = $exception->getMessage();
        }
        return response()->json(['message' => $response]);
    }

    public function runBackup()
    {
        // call song import command
        $logMesaage = [
            'location' => __CLASS__ . ':' . __FUNCTION__,
        ];
        Log::info(json_encode($logMesaage, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        Artisan::call('db:bk', [
            '-p' => 'y'
        ]);

        $output = explode("\n", trim(Artisan::output()));
        $output = array_filter($output, function ($line) {
            return !empty($line);
        });


        return new JsonResponse([
            'message' => 'Backup Run successfully',
            'data' => $output
        ], 200);
    }
}
