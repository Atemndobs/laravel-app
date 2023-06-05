<?php

namespace App\Http\Controllers\Api\Storage;

use App\Console\Commands\Indexer\MeilisearchReindexer;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;

class FileAccessRefreshController extends Controller
{
    public function execute()
    {
       $shell =  shell_exec('chmod -R 777 /var/www/html/');
       $process = Process::run(['chmod -R 777 /var/www/html/'])->output();

        return new JsonResponse([
            'message' => 'File access refreshed successfully',
            'data' => json_encode([$shell, $process], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
        ], 200);
    }
}
