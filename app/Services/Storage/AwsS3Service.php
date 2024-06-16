<?php

namespace App\Services\Storage;

use Aws\S3\S3Client;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Console\Style\OutputStyle;

class AwsS3Service
{
    protected S3Client $s3Client;
    protected $maxRetries;
    protected $delay;
    protected ?OutputStyle $output = null;

    public function __construct($maxRetries = 5, $delay = 5)
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

        $this->maxRetries = $maxRetries;
        $this->delay = $delay;
    }

    public function setOutput(OutputStyle $output): void
    {
        $this->output = $output;
    }

    protected function writeToConsole($message, $type = 'info'): void
    {
        if ($this->output) {
            switch ($type) {
                case 'info':
                    $this->output->info($message);
                    break;
                case 'warn':
                    $this->output->warning($message);
                    break;
                case 'error':
                    $this->output->error($message);
                    break;
                case 'comment':
                    $this->output->comment($message);
                    break;
                default:
                    $this->output->writeln($message);
                    break;
            }
        }
    }


    protected function retry(callable $callback)
    {
        $retryCount = 0;
        $success = false;
        $result = null;

        while ($retryCount < $this->maxRetries && !$success) {
            try {
                $result = $callback();
                $success = true;

            } catch (Exception $e) {
                $retryCount++;
                $message = [
                    '-----------  AWS S3 RETRY operation successful  -----------------',
                    '$retryCount' => $retryCount,
                    '$maxRetries' => $this->maxRetries,
                    '$delay' => $this->delay,
                    'error' => $e->getMessage(),
                ];
                Log::error(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                $this->writeToConsole(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), 'error');
                if ($retryCount < $this->maxRetries) {
                    sleep($this->delay);
                } else {
                    throw $e;
                }
            }
        }
        return $result;
    }

    /**
     * @throws Exception
     */
    public function uploadFile($filePath, $bucket, $key): string
    {
        return $this->retry(function() use ($filePath, $bucket, $key) {
            $checkFile = $this->s3Client->doesObjectExist($bucket, $key);
            dump([
                'checkFile' => $checkFile,
                'bucket' => $bucket,
                'key' => $key,
            ]);
            if ($checkFile) {
                $fileInfo = $this->s3Client->headObject([
                    'Bucket' => $bucket,
                    'Key' => $key,
                ]);
                dump($fileInfo);
                if ($fileInfo) {
                    $url = $this->s3Client->getObjectUrl($bucket, $key);
                    $url = $this->cleanUrl($url);

                    $message = [
                        'status' => 'File already exists in ' . $bucket . '/' . $key,
                        'url' => $url,
                    ];
                    Log::info(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                    return $url;
                }
            }

            $result = $this->s3Client->putObject([
                'Bucket' => $bucket,
                'Key' => $key,
                'SourceFile' => $filePath,
            ]);
            dump([
                'result' => $result
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
        });
    }

    public function putObject(string $filePath, string $dir = 'music'): string
    {
        $key = $dir . '/' . basename($filePath);
        return $this->uploadFile($filePath, env('AWS_BUCKET'), $key);
    }

    public function deleteMusic(string $name): void
    {
        $key = 'music/' . $name;
        $this->retry(function() use ($key) {
            $this->s3Client->deleteObject([
                'Bucket' => env('AWS_BUCKET'),
                'Key' => $key,
            ]);
            Log::info('File deleted successfully from ' . env('AWS_BUCKET') . '/' . $key);
        });
    }

    public function getFilesSmallerThan1MB(string $dir = 'music'): array
    {
        $files = [];
        $objects = $this->retry(function() use ($dir) {
            return $this->s3Client->getIterator('ListObjects', [
                'Bucket' => env('AWS_BUCKET'),
                'Prefix' => $dir . '/',
            ]);
        });

        foreach ($objects as $object) {
            $size = $object['Size'];
            if ($size < 1048576) {
                $files[] = $object['Key'];
            }
        }
        return $files;
    }



    public function deleteMany(array $files, string $dir = 'music'): void
    {
        foreach ($files as $file) {
            $key = $dir . '/' . $file;
            $this->retry(function() use ($key) {
                $this->s3Client->deleteObject([
                    'Bucket' => env('AWS_BUCKET'),
                    'Key' => $key,
                ]);
                Log::info('File deleted successfully from ' . env('AWS_BUCKET') . '/' . $key);
            });
        }
    }

    public function getFiles(string $dir = 'music'): array
    {
        $files = [];
        $objects = $this->retry(function() use ($dir) {
            return $this->s3Client->getIterator('ListObjects', [
                'Bucket' => env('AWS_BUCKET'),
                'Prefix' => $dir . '/',
            ]);
        });

        foreach ($objects as $object) {
            $files[] = $object['Key'];
        }
        return $files;
    }

    public function getObjects(string $dir = 'music'): object
    {
        return $this->retry(function() use ($dir) {
            return $this->s3Client->getIterator('ListObjects', [
                'Bucket' => env('AWS_BUCKET'),
                'Prefix' => $dir . '/',
            ]);
        });
    }

    public function getUnsortedSongs()
    {
        $unsortedFiles = [];
        $audioFiles = Storage::disk('public')->allFiles('uploads/audio');
        $audioFiles_before = $audioFiles;
        $audioFiles = array_merge($audioFiles, Storage::disk('public')->allFiles('uploads/audio/*'));
        $audioFiles = array_filter($audioFiles, function ($file) {
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

    /**
     * @throws Exception
     */
    public function getObject(string $dir, string $file)
    {
        return $this->retry(function() use ($dir, $file) {
            $key = $dir . '/' . $file;
            $checkFile = $this->s3Client->doesObjectExist(env('AWS_BUCKET'), $key);
            if ($checkFile) {
                $fileInfo = $this->s3Client->headObject([
                    'Bucket' => env('AWS_BUCKET'),
                    'Key' => $key,
                ]);
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
        });
    }

    public function getObjectUrl(string $key)
    {
        return $this->retry(function() use ($key) {
            return $this->s3Client->getObjectUrl(env('AWS_BUCKET'), $key);
        });
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
        return $this->retry(function() use ($key) {
            return $this->s3Client->deleteObject([
                'Bucket' => env('AWS_BUCKET'),
                'Key' => $key,
            ]);
        });
    }

    /**
     * @throws Exception
     */
    public function getIterator($operation, array $parameters = [])
    {
        return $this->retry(function() use ($operation, $parameters) {
            return $this->s3Client->getIterator($operation, $parameters);
        });
    }

    public function cleanUrl($path): string
    {
        return str_replace(':9000', '', $path);
    }
}