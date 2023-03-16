<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ConfigCompareCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'con:fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        // load the txt files pro_config.txt and staging_config.txt from storage into arrays
        $proConfig = file(storage_path('app/mysql_config_PROD.txt'));
        $stagingConfig = file(storage_path('app/mysql_config_STAGE.txt'));
        // clean up the arrays and remove all lines  starting with ****
        $proConfig = array_filter($proConfig, function ($line) {
            return strpos($line, '****') === false;
        });
        $stagingConfig = array_filter($stagingConfig, function ($line) {
            return strpos($line, '****') === false;
        });

        // remove \r\n from the end of each line
        $proConfig = array_map(function ($line) {
            return trim($line);
        }, $proConfig);
        $stagingConfig = array_map(function ($line) {
            return trim($line);
        }, $stagingConfig);
        // remove all lines that are empty
        $proConfig = array_filter($proConfig);
        $stagingConfig = array_filter($stagingConfig);
        // count the number of lines in each array
        $proConfigCount = count($proConfig);
        $stagingConfigCount = count($stagingConfig);
        // renumber the arrays
        $proConfig = array_values($proConfig);
        $stagingConfig = array_values($stagingConfig);
        // create a new array to hold the differences
        $diff = [];
        // loop through the proConfig array and odd lines as key and even lines as values
        foreach ($proConfig as $key => $value) {
            if ($key % 2 == 0) {
                // remove varable name: from the start of each key and remove value from the end of each value
                $value = str_replace('Variable_name:', '', $value);
                $new_value = str_replace('Value:', '', $proConfig[$key + 1]);
                $proConfig[$value] = $new_value;
            }
        }

        // loop through the stagingConfig array and odd lines as key and even lines as values
        foreach ($stagingConfig as $key => $value) {
            if ($key % 2 == 0) {
                // remove varable name: from the start of each key and remove value from the end of each value
                $value = str_replace('Variable_name:', '', $value);
                $new_value = str_replace('Value:', '', $stagingConfig[$key + 1]);
                $stagingConfig[$value] = $new_value;
            }
        }

        // loop through the proConfig array and compare each key and value to the stagingConfig array
        foreach ($proConfig as $key => $value) {
            if (array_key_exists($key, $stagingConfig)) {
                if ($value != $stagingConfig[$key]) {
                    if (!is_int($key)){
                        $diff[$key] = [
                            'prod_value' => $value,
                            'staging_value' => $stagingConfig[$key]
                        ];
                    }
                }
            }
        }
        // write the differences to a file
        $file = fopen(storage_path('app/mysql_config_diff.json'), 'w');

            fwrite($file, json_encode($diff));

        fclose($file);
        // print the differences to the console

        // convert file to table
        dd($diff);
    }
}
