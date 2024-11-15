<?php

namespace App\Http\Controllers\Api\Commands;

use App\Http\Controllers\Controller;
use App\Services\Ocr\ExtractService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OcrCommandController extends Controller
{
    protected $extractService;

    // Inject ExtractService via dependency injection
    public function __construct(ExtractService $extractService)
    {
        $this->extractService = $extractService;
    }

    // Execute OCR on the uploaded image
    public function execute(Request $request)
    {
        // Validate the request to ensure an image file is provided
        $request->validate([
            'image' => 'required|file|mimes:jpg,jpeg,png',
        ]);

        // Define the directory for storing images if it doesn't exist
        $dir = storage_path('app/public/uploads/ocr');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Store the uploaded image in the specified directory
        $imagePath = $request->file('image')->store('public/uploads/ocr');
        $fullImagePath = storage_path("app/$imagePath");

        try {
            // Call the extractTextFromImage method to get extracted text
            $extractedText = $this->extractTextFromImage($fullImagePath);
            $cleanedText = $this->extractService->cleanExtractedText($extractedText);
            $categorizedText = $this->extractService->categorizeDocument($cleanedText);

            Log::debug(
                json_encode(
                    $categorizedText,
                    JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
                )
            );

            $reformatted =  $this->extractService->parseTableToKeyValueArray($categorizedText);
        } catch (\Exception $exception) {
            Log::error(
                json_encode(
                    $exception->getMessage(),
                    JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
                )
            );
            return response()->json(['error' => $exception->getMessage()], 500);
        }

        // Clean up by deleting the image file after extraction
        Storage::delete($imagePath);

        // Return the extracted text as a JSON response
        return response()->json($reformatted);
    }

    // Private function to extract text from the image using ExtractService
    private function extractTextFromImage(string $imagePath)
    {
        try {
            Log::channel('soundcloud')->error(json_encode("Starting text extraction for image: $imagePath", JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

            $extractedText = $this->extractService->extractTextFromImage($imagePath);

            Log::error(json_encode("Extracted text: $extractedText", JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

            return $extractedText;
        } catch (\Exception $exception) {
            Log::channel('soundcloud')->error(json_encode($exception->getMessage(), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
            throw $exception; // Rethrow to handle in the calling function
        }
    }


    // Execute OCR on all images in a directory
    public function extractTextFromImagesInDirectory(string $directoryPath)
    {
        try {
            Log::channel('soundcloud')->error(json_encode("Starting text extraction for images in directory: $directoryPath", JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

            $files = glob($directoryPath . '/*');
            foreach ($files as $file) {
                $extractedText = $this->extractService->extractTextFromImage($file);
                $outputFile = $directoryPath . '.json';
                file_put_contents($outputFile, json_encode(['file' => $file, 'text' => $extractedText]) . PHP_EOL, FILE_APPEND);

                Log::channel('soundcloud')->error(json_encode("Extracted text from file: $file", JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
            }
        } catch (\Exception $exception) {
            Log::channel('soundcloud')->error(json_encode($exception->getMessage(), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        }
    }
}
