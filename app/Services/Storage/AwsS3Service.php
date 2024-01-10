<?php

namespace App\Services\Storage;

use Aws\S3\S3Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AwsS3Service
{
    protected S3Client $s3Client;

    public function __construct()
    {
        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region' => env('AWS_REGION', 'us-east-1'),
            'endpoint' => env('AWS_ENDPOINT', 'https://s3.amazonaws.com'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', true),
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);
    }

    public function uploadFile($filePath, $bucket, $key): string
    {
        $checkFile = $this->s3Client->doesObjectExist($bucket, $key);
        // if file exists, get the file info
        if ($checkFile) {
            $fileInfo = $this->s3Client->headObject([
                'Bucket' => $bucket,
                'Key' => $key,
            ]);
            // if file exists, get the file info
            if ($fileInfo) {
                $url = $this->s3Client->getObjectUrl($bucket, $key);
                $message = [
                    'status' => 'File already exists in ' . $bucket . '/' . $key,
                    'url' => $url,
                ];
                Log::info(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                return $url;
            }
        }

        try {
            $result = $this->s3Client->putObject([
                'Bucket' => $bucket,
                'Key' => $key,
                'SourceFile' => $filePath,
            ]);
            Log::info('File uploaded successfully to ' . $bucket . '/' . $key);
            $message = [
                'status' => 'success',
                's3_result' => $result,
                'message' => 'File uploaded successfully to ' . $bucket . '/' . $key,
            ];
            $url = $this->s3Client->getObjectUrl($bucket, $key);
            Log::info(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            return $url;
        } catch (\Aws\Exception\AwsException $e) {
            Log::error('Error uploading file: ' . $e->getMessage());
            return 'Error uploading file: ' . $e->getMessage();
        }
    }

    public function putObject(string $filePath, string $dir = 'music'): string
    {
        $key = $dir . '/' . basename($filePath);
        return $this->uploadFile($filePath, env('AWS_BUCKET'), $key);
    }

    public function deleteMusic(string $name): void
    {
        $key = 'music/' . $name;
        try {
            $this->s3Client->deleteObject([
                'Bucket' => env('AWS_BUCKET'),
                'Key' => $key,
            ]);
            Log::info('File deleted successfully from ' . env('AWS_BUCKET') . '/' . $key);
        } catch (\Aws\Exception\AwsException $e) {
            Log::error('Error deleting file: ' . $e->getMessage());
        }
    }

    // function to get all files in a directory
    public function getFiles(string $dir = 'music'): array
    {
        $files = [];
        $objects = $this->s3Client->getIterator('ListObjects', [
            'Bucket' => env('AWS_BUCKET'),
            'Prefix' => $dir . '/',
        ]);
        foreach ($objects as $object) {
            $files[] = $object['Key'];
        }
        return $files;
    }

    public function getUnsortedSongs()
    {
        $unsortedFiles = [];
        // get unsorted files from /var/www/html/storage/app/public/uploads/audio/ or any subfolder
        $audioFiles = Storage::disk('public')->allFiles('uploads/audio');
        $audioFiles_before = $audioFiles;
        $audioFiles = array_merge($audioFiles, Storage::disk('public')->allFiles('uploads/audio/*'));
        $audioFiles = array_filter($audioFiles, function ($file) {
            // only get files that end with mp3 and are not less than 1MB
//            if (Storage::disk('public')->size($file) < 1000000) {
//                return false;
//            }
            return Str::endsWith($file, 'mp3');
        });

        dd([
            'audioFiles' => count($audioFiles),
            '$audioFiles_before' => count($audioFiles_before),
        ]);
    }

    public function putObjectWithFileName(string $file, string $dir, string $filename)
    {
        $key = $dir . '/' . $filename;
        return $this->uploadFile($file, env('AWS_BUCKET'), $key);
    }

    public function getObject(string $dir, string $file)
    {
        $key = $dir . '/' . $file;
        $checkFile = $this->s3Client->doesObjectExist(env('AWS_BUCKET'), $key);
        // if file exists, get the file info
        if ($checkFile) {
            $fileInfo = $this->s3Client->headObject([
                'Bucket' => env('AWS_BUCKET'),
                'Key' => $key,
            ]);
            // if file exists, get the file info
            if ($fileInfo) {
                $url = $this->s3Client->getObjectUrl(env('AWS_BUCKET'), $key);
                $message = [
                    'status' => 'File already exists in ' . env('AWS_BUCKET') . '/' . $key,
                    'url' => $url,
                ];
                Log::warning(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                return $url;
            }
        }
        Log::error('File does not exist in ' . env('AWS_BUCKET') . '/' . $key);
        throw new \Exception('File does not exist in ' . env('AWS_BUCKET') . '/' . $key);
        // return 'File does not exist in ' . env('AWS_BUCKET') . '/' . $key;
    }

    public function deleteFile(string $dir, string $file)
    {
        $key = $dir . '/' . $file;
        $info = [
            'bucket' => env('AWS_BUCKET'),
            'key' => $key,
        ];
        dump($info);
        Log::info(json_encode($info, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        try {
            $result =  $this->s3Client->deleteObject([
                'Bucket' => env('AWS_BUCKET'),
                'Key' => $key,
            ]);
            Log::info('File deleted successfully from ' . env('AWS_BUCKET') . '/' . $key);
            return $result;
        } catch (\Aws\Exception\AwsException $e) {
            Log::error('Error deleting file: ' . $e->getMessage());
        }

    }
}