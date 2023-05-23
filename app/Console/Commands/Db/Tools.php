<?php

namespace App\Console\Commands\Db;

use Illuminate\Support\Facades\DB;
use ZipArchive;

trait Tools
{
    /**
     * @param  bool|array  $files
     * @param  int  $count
     * @return mixed
     */
    public function backupTable(bool|array $files, int $count): mixed
    {
        $data = [];
        foreach ($files as $file) {
            if (is_dir($file)) {
                continue;
            }
            // get the latest file by date in file name
            $fileName = basename($file);
            $fileDate = substr($fileName, 0, 19);
            $date = date_create_from_format('Y-m-d-H-i-s', $fileDate);
            // if data is today's date, actualDate = Today, else date = YYYY-MM-DD
            // $date->format('Y-m-d') == date('Y-m-d') ? 'Today' : $date->format('Y-m-d')
            $actualDate = $date->format('Y-m-d') == date('Y-m-d') ? '<fg=green>Today at </>' : "<fg=yellow>{$date->format('Y-m-d')}</>";

            // styles black, red, green, yellow, blue, magenta, cyan, white, default, gray, bright-red, bright-green,
            //  bright-yellow, bright-blue, bright-magenta, bright-cyan, bright-white
            // add style to name, date and time. highlight the last date and time
            $data[] = [
                'file' => count($data) + 1,
                'name' => "<fg=default>$fileName</>",
                'date' => $actualDate,
                // if file is the last file in this array, highlight the time with red background
                'time' => $file === $files[$count - 1] ? "<fg=white;bg=bright-magenta>{$date->format('H:i:s')}</>" : "<fg=yellow;>{$date->format('H:i:s')}</>",
            ];
        }
        $this->table(['file', 'name', 'date', 'time'], $data);

        return $file;
    }

    /**
     * @param  string  $filepath
     * @param  string  $destination
     * @return bool|string
     */
    public function unzipFile(string $filepath, string $destination): bool|string
    {
        // unzip file from filepath and return the unzipped file path
        $zip = new ZipArchive;
        $res = $zip->open($filepath);
        if ($res === true) {
            $zip->extractTo($destination);
            $unzippedFile = $zip->getNameIndex(0);
            $zip->close();

            return $destination.$unzippedFile;
        } else {
            return false;
        }
    }

    /**
     * @param  string  $latestFile
     * @return void
     */
    public function downloadFileFromBackupFolder(string $latestFile): void
    {
        // download latest file from backup folder
        $this->info('Downloading file: '.$latestFile);
        $file = storage_path('app/backups/'.$latestFile);
        $this->info('File downloaded');
        // unzip file
        $this->info('Unzipping file');
        $destination = storage_path('app/');
        $unzippedFile = $this->unzipFile($file, $destination);
        // import dump
        DB::unprepared(file_get_contents($unzippedFile));
        $this->info('Dump imported');
        // delete file
        $this->info('Deleting file');
        unlink($unzippedFile);
        $this->info('File deleted');
    }
}
