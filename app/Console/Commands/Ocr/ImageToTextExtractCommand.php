<?php

namespace App\Console\Commands\Ocr;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ImageToTextExtractCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ocr:extract {--i|imagePath= : The path to the image file} {--d|directoryPath= : The path to the directory containing images} {--f|fileName= : The name of the file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extract text from an image using OCR (Optical Character Recognition)
    options -i imagePath, -d directoryPath, -f fileName
    example: php artisan ocr:extract -i storage/app/images/image.jpg
    example: php artisan ocr:extract -d storage/app/public/finops
    
    ';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $imagePath = $this->option('imagePath');
        $directoryPath = $this->option('directoryPath');
       // $fileName = $this->option('fileName')??'output';
        if ($imagePath) {
            $this->extractTextFromImage($imagePath);
        } elseif ($directoryPath) {
            $this->extractTextFromImagesInDirectory($directoryPath);
        } else {
            $this->error('Please provide an image path or a directory path');
        }

    }

    private function extractTextFromImage(bool|array|string $imagePath)
    {
        // Extract text from image and display it in the console
        $this->info("Extracting text from image: $imagePath");
        $extractedText = app('App\Services\Ocr\ExtractService')->extractTextFromImage($imagePath);
        $this->info("Extracted text: $extractedText");

    }

    private function extractTextFromImagesInDirectory(bool|array|string $directoryPath)
    {
        // Extract text from images in a directory and display it in the console
        $this->info("Extracting text from images in directory: $directoryPath");
        $files = glob($directoryPath . '/*');


        foreach ($files as $file) {
            $extractedText = app('App\Services\Ocr\ExtractService')->extractTextFromImage($file);
            $outputFile = $directoryPath . '.txt';
            file_put_contents($outputFile, $extractedText . PHP_EOL, FILE_APPEND);
            $this->info("Extracted text: $extractedText");
        }

    }
}
