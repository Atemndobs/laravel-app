<?php

namespace App\Console\Commands\Storage;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GetCountriesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'countries:get';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get all countries and save to json file in storage/app/public/countries.json';

    /**
     * Execute the console command.
     */
    public function handle()
    {

// Make the API request
        $response = file_get_contents('https://restcountries.com/v3.1/all');

// Check if the request was successful
        if ($response !== false) {
            // Decode the JSON response
            $data = json_decode($response, true);

            // Process the response data and generate the JSON objects
            try {
                $countries = array_map(function($country) {
                    return [
                        'id' => '', // Assign a unique ID for each country
                        'iso' => $country['cca2'],
                        'name' => $country['name']['common'],
                        'nicename' => '', // Add appropriate value if available
                        'iso3' => $country['cca3'],
                        'numcode' => '', // Add appropriate value if available
                        'phonecode' => $country['callingCode'][0],
                        'createdAt' => '', // Add appropriate value if needed
                        'updatedAt' => '', // Add appropriate value if needed
                    ];
                }, $data);
            }catch (\Exception $e){
                echo 'Error: Failed to process data from the API.';
                $errorMessage = $e->getMessage();
                $message = [
                    'response' => $data,
                    'errorMessage' => $errorMessage,
                ];
                Log::error(json_encode($message, JSON_PRETTY_PRINT));
                $file = "/var/www/html/storage/app/public/data/data.json";
                file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
                return;
            }

            // Convert the array of countries to JSON
            $json = json_encode($countries, JSON_PRETTY_PRINT);

            if ($json !== false) {
                // Write the JSON data to a file

                // file location: /var/www/html/storage/app/public/countries.json
                $file = "/var/www/html/storage/app/public/countries.json";
                file_put_contents($file, $json);

                echo 'Data successfully written to countries.json file.';
                Log::info('Data successfully written to countries.json file.');
            } else {
                echo 'Error: Failed to encode data as JSON.';
                Log::error('Error: Failed to encode data as JSON.');
            }
        } else {
            echo 'Error: Failed to retrieve data from the API.';
            Log::error('Error: Failed to retrieve data from the API.');
        }
    }
}
