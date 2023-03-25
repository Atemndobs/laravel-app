<?php

namespace App\Services\Storage;

use AllowDynamicProperties;
use Aws\S3\S3Client as Client;
use Illuminate\Support\Facades\Storage;


/**
 * Class Minio
 * @package App\Services\Storage
 */
#[AllowDynamicProperties] class MinioService
{
    public string $bucket;
    public \Illuminate\Contracts\Filesystem\Filesystem $disk;

    /**
     *
     */
    public function __construct()
    {
        $this->bucket = env('AWS_BUCKET', 'curator');
        $this->disk = Storage::disk('s3');
    }

    /**
     * @param string $dir
     * @return array
     */
    public function getAllObjects(string $dir = ''): array
    {
        return $this->disk->allFiles($dir);
    }

    /**
     * @param string $file_name
     * @param string $dir
     * @return string
     */
    public function getAudio(string $file_name, string $dir = 'music'): string
    {
        return $this->getUrl($file_name);
    }

    /**
     * @param string $dir
     * @return array
     */
    public function getAllAudios(string $dir = 'music'): array
    {
        return $this->getAllObjects($dir);
    }

    /**
     * @param string $file_name
     * @param string $dir
     * @return string
     */
    public function getImage(string $file_name, string $dir = 'images'): string
    {
        return $this->getUrl($file_name, $dir);
    }

    /**
     * @param string $dir
     * @return array
     */
    public function getAllImages(string $dir = 'images' ): array
    {
        return $this->getAllObjects($dir);
    }

    /**
     * @param string $contents
     * @param string $dir
     * @return string
     * @throws \Exception
     */
    public function putObject(string $contents, string $dir = 'music'): string
    {
        $path = $dir . '/' . basename($contents);
        $filename = basename($contents);
        try {
            $this->disk->put($path, file_get_contents($contents), 'public');
        }catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return $this->disk->url($dir . '/' . $filename );
    }

    /**
     * @param string $file
     * @param string $dir
     * @return string
     * @throws \Exception
     */
    public function getUrl(string $file, string $dir = 'music'): string
    {
        $path = $this->bucket . '/' . $dir . '/' . $file;
        if ($this->disk->exists($dir . '/' . $file)) {
            return $this->disk->url($path);
        }
       # return $this->disk->url($path);
        $e = new \Exception('File not found: ' . $path);
        throw new \Exception($e->getMessage());
    }

    /**
     * @param string $path
     * @param int $expires
     * @return string
     */
    public function getTempUrl(string $path, int $expires): string
    {
        return $this->disk->temporaryUrl(
            $path, // The path to the file
            now()->days($expires), // Set the expiration time for the URL
            [
                'Bucket' => $this->bucket,
                'ResponseContentType' => 'audio/mpeg', // Set the content type for the response
            ]
        );
    }
}
