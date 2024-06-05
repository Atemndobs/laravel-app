<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Aws\S3\S3Client;

class RetryableS3Client
{
    protected $s3Client;
    protected $maxRetries;
    protected $delay;

    public function __construct(S3Client $s3Client, $maxRetries = 5, $delay = 5)
    {
        $this->s3Client = $s3Client;
        $this->maxRetries = $maxRetries;
        $this->delay = $delay;
    }

    public function getIterator($operation, array $parameters = [])
    {
        $retryCount = 0;
        $success = false;
        $result = null;

        while ($retryCount < $this->maxRetries && !$success) {
            try {
                $result = $this->s3Client->getIterator($operation, $parameters);
                $success = true;
            } catch (Exception $e) {
                $retryCount++;
                Log::error("Error performing $operation: " . $e->getMessage());
                if ($retryCount < $this->maxRetries) {
                    sleep($this->delay);
                } else {
                    throw $e;
                }
            }
        }

        return $result;
    }

    // Add other S3 methods here as needed
}