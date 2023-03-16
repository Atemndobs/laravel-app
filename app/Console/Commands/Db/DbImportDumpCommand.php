<?php

namespace App\Console\Commands\Db;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class DbImportDumpCommand extends Command
{
    use Tools;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // ask if you want to import dump
        $this->question('Do you want to import dump? (y/n)');
        $answer = $this->ask('Do you want to import dump? (y/n)');
        if ($answer == 'y') {
            // import dump
            $this->info('Importing dump');
            // get file from backups folder
            $files = glob(storage_path('app/backups/*'));
            // exclud directories from $files array
            $files = array_filter($files, function ($file) {
                return !is_dir($file);
            });
            $count = count($files);
            if ($count === 0) {
                $this->info('No files found');

                return 0;
            }
            $latestDate = 0;
            $latestFile = '';
            $data = [];
            if ($count > 1) {
                $dates = [];
                foreach ($files as $file) {
                    $dates[] = Carbon::createFromTimestamp(filemtime($file));
                    if (count($dates) > 1) {
                        $latestDate = max($dates);
                        $fileName = basename($file);
                        $latestFile = $fileName;
                    }
                }

            } else {
                $latestFile = basename($files[0]);
                $this->info("only one file in backup folder | $latestFile)");
                // ask to continue or not
                $this->question('Do you want to continue? (y/n)');
                $answer = $this->ask('Do you want to continue? (y/n)');
                if ($answer == 'y') {
                    $this->downloadFileFromBackupFolder($latestFile);
                    $this->line("<fg=blue>Restored Backup from :   $latestFile  </>");

                    return 0;
                }
                $this->line('<fg=red> Backup Restore Aborted !!!</>');

                return 0;
            }
        } else {
            $this->line('<fg=red> Backup Restore Aborted !!!</>');

            return 0;
        }

        // table with all files and dates
        $this->backupTable($files, $count);
        // show all backups
        $this->info('Backups:');
        $this->call('backup:list');
        // ask to restore latest backup
        $this->question('Do you want to restore latest backup? (y/n)');
        $answer = $this->ask('Do you want to restore latest backup? (y/n)');
        if ($answer == 'y') {
            // restore latest backup
            $this->info('Restoring latest backup');
            $this->downloadFileFromBackupFolder($latestFile);
            $date = Carbon::createFromTimestamp($latestDate);
            $date = $date->format('Y-m-d-H-i-s');
            $this->line("<fg=blue> Restored $latestFile from $date  </>");
        } else {
            // chose backup to restore
            $import = $this->choice('Select file to import', $files);
            $fileName = basename($import);
            $this->downloadFileFromBackupFolder($fileName);
            // print line with style : blue color
            $this->line("<fg=blue>Restored from file:   $fileName  </>");
        }

        return 0;
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
