<?php

namespace App\Services\Storage;

use Aws\S3\S3Client;
use Illuminate\Support\Facades\Log;

class AwsS3Service
{
    protected S3Client $s3Client;

    public function __construct()
    {
        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region'  => env('AWS_REGION', 'us-east-1'),
            'endpoint' => env('AWS_ENDPOINT', 'https://s3.amazonaws.com'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', true),
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
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
                Log::info(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) );
                return $url;
            }
        }

        try {
            $result = $this->s3Client->putObject([
                'Bucket' => $bucket,
                'Key'    => $key,
                'SourceFile' => $filePath,
            ]);
            Log::info('File uploaded successfully to ' . $bucket . '/' . $key);
            $message = [
                'status' => 'success',
                's3_result' => $result,
                'message' => 'File uploaded successfully to ' . $bucket . '/' . $key,
            ];
            $url = $this->s3Client->getObjectUrl($bucket, $key);
            Log::info(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) );
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
}