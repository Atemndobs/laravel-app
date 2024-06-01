<?php

namespace App\Console\Commands\Storage;

use Illuminate\Console\Command;
use App\Services\Storage\AwsS3Service;
use Illuminate\Support\Facades\Log;

class AwsS3DownloadCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 's3:dn {--d|dir=} {--b|bucket=curators3}  {--l|location=downloads} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get count of files from s3 bucket folders : music, images, backups, assets. 
    option --dir to get files from specific folder';

    private AwsS3Service $s3Service;

    public function __construct(AwsS3Service $s3Service)
    {
        parent::__construct();
        $this->s3Service = $s3Service;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // get all files in music folder and images folder from s3 bucket and count them
        $dir = $this->option('dir');
        $bucket = $this->option('bucket');
        $location = $this->option('location');
        $destination = "$location/$dir";
        $options = ([
            'dir' => $dir,
            'bucket' => $bucket,
            'location' => $location,
            'destination' => $destination,
        ]);



        $this->warn(json_encode(['options' => $options], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        if ($dir !== null) {
            try {
                $s3Objects = $this->s3Service->getFiles($dir);
                $this->downloadMultipleFiles($s3Objects, $destination);
            } catch (\Exception $e) {
                $this->line("<fg=red>{$e->getMessage()}</>");
                Log::error($e->getMessage());
                // wait for 5 seconds
                sleep(5);
                //retry
                $s3Objects = $this->s3Service->getFiles($dir);
                $this->downloadMultipleFiles($s3Objects, $destination);
            }
            $message = [
                'stats' => [
                    'directory' => $dir,
                    'bucket' => $bucket
                ]
            ];
            $this->info(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
        // nothing was downloaded
        $this->error(
            'No Folder was Downloaded. Pls recheck'
        , JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        return 0;
    }

    private function downloadBackup(string $object, string $dir = 'backups'): void
    {
        $fileName = basename($object);
        $path = storage_path("app/$dir/" . $fileName);
        $this->downloadFile($object, $path, $dir);
    }

    public function downloadObject(string $object, string $destination): void
    {
        $fileName = basename($object);
        $path = storage_path("app/public/$destination/" . $fileName);
        $this->downloadFile($object, $path);
    }

    /**
     * @param string $object
     * @param string $path
     * @return void
     */
    public function downloadFile(string $object, string $path): void
    {
        // check if file exists
        if (file_exists($path)){
            $this->warn(json_encode([
                'FILE EXISTS',
                $path
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            Log::warning('File exists: ' . $path);
            return;
        }
        $this->info('Downloading file...');
        try {
            $file = file_get_contents($object);
        }catch (\Exception $e){
            $this->error($e->getMessage());
            Log::error($e->getMessage());
            // wait for 5 seconds
            sleep(5);
            // retry
            $file = file_get_contents($object);
        }
        $this->info('File downloaded successfully');
        $this->info('Writing file to disk...');

        file_put_contents($path, $file);
        $this->info('File written to disk successfully');
        $this->info('File path: ' . $path);
        $message = [
            'file_name' => $object,
            'file_path' => $path,
        ];
        $this->line("<fg=bright-magenta>" . json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "</>");
        Log::info('File written to disk successfully' , $message);
    }

    public function downloadMultipleFiles(array $objects, string $destination) :void
    {
        $count = count($objects);
        $info = [
            'destination' => $destination,
            'filesCount' => $count
        ];
        $this->line("<fg=bright-magenta>" . json_encode($info, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "</>");
        // start processing bar
        $this->output->progressStart(count($objects));
        $this->line('');
        $completed = 0;
        foreach ($objects as $object) {
            try {
                $url = $this->s3Service->getObjectUrl($object);
                $this->downloadObject($url, $destination);
            }catch (\Exception $e){
                $this->error($e->getMessage());
                Log::error($e->getMessage());
                // wait for 5 seconds
                sleep(5);
                // retry
                $url = $this->s3Service->getObjectUrl($object);
                $this->downloadObject($url, $destination);
            }
            $left = $count - $completed;
            $message = [
                'object' => $object,
                'total' => $count,
                'completed' => $completed,
                'left' => $left,
            ];
            Log::info('Status', $message, '');
            $this->output->progressAdvance();
            $this->line('');
            $completed++;
        }
        // finish progress bar
        $this->output->progressFinish();
        $message = [
            'status' => "Download completed to $destination",
            'filesCount' => count($objects)
        ];
        $this->line("<fg=bright-magenta>" . json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "</>");
    }

}
