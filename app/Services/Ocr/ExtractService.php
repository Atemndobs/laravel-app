<?php

namespace App\Services\Ocr;
use thiagoalessio\TesseractOCR\TesseractOCR;
use thiagoalessio\TesseractOCR\TesseractOcrException;

class ExtractService
{
    /**
     * @param string $imagePath
     * @return string
     * @throws TesseractOcrException
     */
    public function extractTextFromImage(string $imagePath)
    {
        $tesseract = new TesseractOCR($imagePath);
        $extracted = $tesseract->run();
        // remove all empty lines frm the extracted text
        $extracted = preg_replace('/^\h*\v+/m', '', $extracted);
        return $extracted;
    }

    /**
     * @throws TesseractOcrException
     */
    public function extractTextFromDirectory(string $directoryPath)
    {
        // get all files in the directory
        $files = glob($directoryPath . '/*');
        foreach ($files as $file) {
            // add output to a file in json format // crete a file with the same name as the directory and append the output to it
            $extractedText = $this->extractTextFromImage($file);
            $outputFile = $directoryPath . '.json';
            $data = [
                'file' => $file,
                'text' => $extractedText
            ];
            dump($data);

            $json = json_encode($data);
            file_put_contents($outputFile, $json . PHP_EOL, FILE_APPEND);
        }
    }

}