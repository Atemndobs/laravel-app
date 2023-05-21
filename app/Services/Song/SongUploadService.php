<?php

namespace App\Services\Song;

use App\Models\Song;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SongUploadService
{
    protected array $uploadedFiles = [];
    protected array $errors = [];
    protected int $uploadedFilesCount = 0;
    protected int $errorsCount = 0;

    /**
     * @return array
     */
    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles;
    }

    /**
     * @param string $uploadedFile
     * @return void
     */
    public function addUploadedFiles(string $uploadedFile): void
    {
        $this->uploadedFiles[] = $uploadedFile;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param string $error
     * @return void
     */
    public function addErrors(string $error): void
    {
        $this->errors[] = $error;
    }

    /**
     * @param array $files
     * @return array
     */
    public function uploadSong(array $files): array
    {
        foreach ($files as $fileUploadKey) {
            foreach ($fileUploadKey as $file) {
                try {
                    $uploadedFile = $this->uploadFile($file);
                } catch (\Exception $e) {
                    $this->addErrors($e->getMessage());
                    continue;
                }
                $this->addUploadedFiles($uploadedFile);
            }
        }

        $response = [
            'uploadedFiles' => $this->getUploadedFiles(),
            'errors' => $this->getErrors() < 1 ? 'No Errors' : $this->getErrors(),
            'uploadedFilesCount' => count($this->getUploadedFiles()),
            'errorsCount' => count($this->getErrors()),
        ];
        if (count($this->getErrors()) < 1) {
            unset($response['errorsCount']);
        }
        return $response;
    }

    /**
     * @param UploadedFile $file
     * @return string
     * @throws \Exception
     */
    public function uploadFile(UploadedFile $file): string
    {
        // check if file is type audio
        if ($file->getMimeType() != 'audio/mpeg') {
            Log::error("Invalid file type: {$file->getMimeType()}");
            throw new \Exception("Invalid file type: {$file->getMimeType()}");
        }
        // reject wav files
        if ($file->getClientOriginalExtension() == 'wav') {
            Log::error("Invalid file type: {$file->getClientOriginalExtension()}");
            throw new \Exception("Invalid file type: {$file->getClientOriginalExtension()}");
        }

        try {
            // get file name
            $fileName = $file->getClientOriginalName();
            // check if file exists in storage/app/public/uploads/audio
            if (Storage::exists("public/uploads/audio/{$fileName}")) {
                return "File already exists: {$fileName}";
            }
            // store file in storage/app/public/uploads/audio
            $file->storeAs('public/uploads/audio', $fileName);
            // return file name
            return $fileName;
        }catch (\Exception $exception) {
            Log::error($exception->getMessage());
            throw new \Exception($exception->getMessage());
        }
    }
}
