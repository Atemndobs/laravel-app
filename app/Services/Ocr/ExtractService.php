<?php

namespace App\Services\Ocr;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
    /**
     * Clean the extracted OCR text to improve readability.
     *
     * @param string $extractedText
     * @return string
     */
    public function cleanExtractedText(string $extractedText)
    {
        // Clean up the text (same as before)
        $extractedText = preg_replace('/\s{2,}/', ' ', $extractedText);  // Replace multiple spaces with a single space
        $extractedText = preg_replace('/[\r\n]+/', ' ', $extractedText);  // Replace newlines with spaces
        $extractedText = preg_replace('/\s{2,}/', ' ', $extractedText);  // Replace multiple spaces with a single space
        $extractedText = trim($extractedText);
        $extractedText = preg_replace('/[^\x20-\x7E]/', '', $extractedText); // Remove non-printable characters
        return $extractedText;
    }

    public function categorizeDocument($cleanedText)
    {
    $system = <<<EOT
    You are a specialized assistant trained to process and extract structured data from OCR results of receipts from businesses in Germany. Your task is to focus only on the purchased items and their associated prices. Ignore all unrelated text, such as addresses, contact details, payment information, timestamps, or tax details, unless they are directly relevant to identifying the items or their prices.

    When responding:
    1. Extract and format the items in a table with two columns: **Item** and **Price (EUR)**.
    2. If multiple quantities of the same item are purchased, calculate the total price for that item and include it in the table.
    3. Sum up all prices and provide the total amount at the end of the table.
    4. Ensure the table is clearly formatted for easy readability.
    5. Handle German-specific notations, such as commas for decimals (e.g., 3,49 EUR), and convert them appropriately if necessary.
    If any part of the receipt is unclear, make a best effort to interpret it while maintaining accuracy. Do not include any unrelated details in the output.
    EOT;
        // Define the prompt to include in the user message for structured JSON output
//         $prompt = "Please extract and categorize the following OCR text into a structured JSON format with key-value pairs from the reciept.";
        $prompt = "Bitte nur die Einkaufsartikel auflisten mit Preisen in einer Tabelle";
        $system_default = 'Please output the result as structured key-value JSON only.';



        $lamaUrl = "http://45.94.110.90:7090/api/chat";
//         $lmStudioUrl = "http://host.docker.internal:1234/v1/chat/completions";
        $lmStudioUrl = "http://192.168.178.51:1234/v1/chat/completions";    // https://mac.goose-neon.ts.net
        // Prepare the user message content: first the prompt, then the extracted text
        $userMessageContent = json_encode([
            'extracted_text' => $cleanedText
        ]);

//         $model = "hugging-quants/llama-3.2-1b-instruct";
//          $model = 'llama3.2:1b';
         $model = 'llama3.2:1b';

        // Make the API request to Ollama for document categorization
        $response = Http::post($lamaUrl, [
            'model' => $model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => '',
                ],
                [
                    'role' => 'user',
                    'content' => $prompt . " " . $userMessageContent  // Concatenate prompt and extracted text
                ]
            ],
            "temperature" => 0.1,
            "max_tokens" =>  -1,
            'stream' => false
        ]);


        // Handle the response
        if ($response->successful()) {
            $data = $response->json();

        $extractedJson = $this->getValidOutput($data);
            Log::info(
                json_encode(
                    $extractedJson,
                    JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
                )
            );

            return $extractedJson;
        }
        return [
            'error' => 'Error retrieving suggestions from Ollama.'
        ];
    }

public function getValidOutput(array $data): string
       {
           // Check if the data is from Ollama (has 'message' key)
           if (isset($data['message']['content'])) {
               $ollamaContent = $data['message']['content'];
               if (!empty($ollamaContent) && strpos($ollamaContent, 'Error') === false) {
                   return $ollamaContent;
               }
           }

           // Check if the data is from LM Studio (has 'choices' key)
           if (isset($data['choices'][0]['message']['content'])) {
               $lmStudioContent = $data['choices'][0]['message']['content'];
               if (!empty($lmStudioContent) && strpos($lmStudioContent, 'Error') === false) {
                   return $lmStudioContent;
               }
           }

           // Default error message if no valid result is found
           return 'Error: Categorization output failed to produce a valid result.';
       }

function parseTableToKeyValueArray(string $tableData): array
{
    $lines = explode("\n", $tableData); // Split the string by newline
    $parsedData = [];

    // Start parsing from the third line to skip headers
    foreach (array_slice($lines, 2) as $line) {
        // Split the line by pipe (|) and trim each part
        $columns = array_map('trim', explode('|', $line));

        // Ensure the line has at least two columns (key and value)
        if (count($columns) >= 2) {
            $key = $columns[1] ?? null; // First meaningful column
            $value = $columns[2] ?? null; // Second meaningful column

            if ($key !== null && $value !== null) {
                $parsedData[$key] = $value; // Map key-value pair
            }
        }
    }

    return $parsedData;
}


}
