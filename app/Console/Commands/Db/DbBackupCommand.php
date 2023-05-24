<?php

namespace App\Console\Commands\Db;

use Illuminate\Console\Command;
use Illuminate\Http\File;
use Illuminate\Support\Carbon;

class DbBackupCommand extends Command
{
    use Tools;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:bk';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Back up the Database and delete old backups if needed | Shows list of backups and uses the backup:run --only-db command';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Backup database');
        $this->call('backup:list');

        // count files in backup folder
        $files = glob(storage_path('app/backups/*'));
        $count = count($files);
        if ($count > 4) {
            $this->backupTable($files, $count);

            $this->info('Backup folder contains '.$count.' files');
            $answer = $this->askWithCompletion('Do you want to delete all files? (y/n)', ['y', 'n'], 'n');
            if ($answer == 'y') {
                // delete all files from backup folder
                $this->info('Deleting all files from backup folder');
                foreach ($files as $file) {
                    unlink($file);
                }
                $this->info('All files deleted');
            } else {
                $backupFiles = glob(storage_path('app/backups/*'));
                // chose files to delete from $files array
                foreach ($backupFiles as $file) {
                    $cancel = 'select enter to continue with backup';

                    $cleanupFiles = [];
                    foreach ($backupFiles as $key => $deleteFile) {
                        $cleanupFiles[$key + 1] = $deleteFile;
                    }

                    $cleanupFiles[] = $cancel;
                    $cleanupFiles[] = '';
                    $filesToDelete = $this->choice('Choose files to delete', $cleanupFiles);
                    // if $filesToDelete is empty, break loop
                    if (empty($filesToDelete)) {
                        break;
                    }
                    if ($filesToDelete === $cancel) {
                        break;
                    }
                    // delete files from backup folder
                    $this->info('Deleting files from backup folder');
                    // remove 'cancel' from $files array
                    unset($cleanupFiles[array_search($cancel, $cleanupFiles)]);

                    // remove $filesToDelete from $files array
                    unset($cleanupFiles[array_search($filesToDelete, $cleanupFiles)]);
                    unset($backupFiles[array_search($filesToDelete, $backupFiles)]);
                    unset($cleanupFiles[array_search('', $cleanupFiles)]);
                    // reset $files array keys
                    $backupFiles = array_values($backupFiles);
                    unlink($filesToDelete);
                }
            }
        } else {
            $this->info('Backup folder is empty');
        }
        $this->call('backup:run', [
            '--only-db' => true,
            '--only-files' => false,
            '--disable-notifications' => true,
            '--no-compression',
        ]);
        $this->info('Backup database done');
        $files = glob(storage_path('app/backups/*'));
        foreach ($files as $file) {
            \Illuminate\Support\Facades\File::chmod($file, 0777);
        }
        $this->getLatestFile();
        return 0;
    }

    public function getLatestFile()
    {
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
            $this->extractBackupFile($latestFile);
        } else {
            $latestFile = basename($files[0]);
            $this->info("only one file in backup folder | $latestFile)");
            $answer = $this->ask('Do you want to continue? (y/n)');
            if ($answer == 'y') {
                $this->extractBackupFile($latestFile);
                return 0;
            }
            $this->line('<fg=red> Backup Restore Aborted !!!</>');
            return 0;
        }
    }

    /**
     * @param string $latestFile
     * @return void
     */
    public function extractBackupFile(string $latestFile): void
    {
        $this->info('Downloading file: ' . $latestFile);
        $file = storage_path('app/backups/' . $latestFile);
        $destination = storage_path('app/backups/latest/');
        $unzippedFile = $this->unzipFile($file, $destination);
        $this->line("<fg=blue>Prepared Dump Backup from :   $unzippedFile  </>");
    }
}
