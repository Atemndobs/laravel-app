<?php

namespace App\Console\Commands\Storage;

use App\Models\Song;
use Illuminate\Console\Command;
use App\Services\Storage\AwsS3Service;
use Illuminate\Support\Facades\Storage;

class AwsS3PutManyCommand extends Command
{
    /**
     * Execute the console command.
     */
    protected $signature = 's3:multi-put {--s|source=} {--d|directory=music}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Uploads multiple files to S3';



    private AwsS3Service $s3Service;

    public function __construct(AwsS3Service $s3Service)
    {
        parent::__construct();
        $this->s3Service = $s3Service;
    }

    public function handle()
    {
        $source = $this->option('source');
        $directory = $this->option('directory');

        $sourcePath = ("{$source}");
        if (!str_contains($source, '/')) {
            $sourcePath = "/var/www/html/storage/app/public/uploads/{$source}";
        }
        if (!$source) {
            $this->error('A Source must be specified with -s option. example audio or images');
            return 1;
        }


        if (!file_exists($sourcePath)) {
            $this->error("Source {$sourcePath} does not exist.");
            return 1;
        }

        // if source is images, look for .jpg files or .jpeg files
        if ($source === 'images') {
            $files = glob($sourcePath . '/*.jpg');
            $files = array_merge($files, glob($sourcePath . '/*.jpeg'));
        }elseif ($source === 'music') {
            $files = glob($sourcePath . '/*.mp3');
        }else {
            // get only files not directories
            $files = array_filter(glob($sourcePath . '/*'), 'is_file');
        }
        // for each file, upload it to s3
        $count = count($files);
        $this->info("Found $count files to upload");
        $bar = $this->output->createProgressBar($count);
        $bar->start();
        $processed = [];

        $info = [
            'options' => $this->options(),
            //'files' => $files,
            'location' => $sourcePath,
            //'ls' => scandir($sourcePath),
            'files_count' => $count,
        ];
        $this->warn(json_encode($info, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        //$file = $this->getAllSongsFilesFromTheS3BucketMusicFolder($directory, $files);

        foreach ($files as $file) {
            $file_name = basename($file);
            $key = "{$directory}/{$file_name}";
            try {
                $result = $this->s3Service->uploadFile($file, env('AWS_BUCKET'), $key);
                $processed[] = $file;
                // delete file
                $delete = unlink($file);
                if (!$delete) {
                    $this->error("Could not delete file $file");
                }
            }catch (\Exception $e){
                $this->error($e->getMessage());
                continue;
            }
            $bar->advance();
            $message = [
                'status' => 'success',
                's3_result' => $result,
                'deleted? ' => $delete,
                'message' => 'File uploaded successfully to ' . env('AWS_BUCKET') . '/' . $key,
            ];
            $this->line("<fg=bright-magenta>" . json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES). "</>");
        }

        $message = [
            'status' => 'success',
            'uploaded_files' => count($processed),
            'message' => 'Files uploaded successfully to ' . env('AWS_BUCKET') . '/' . $directory,
        ];

        $this->info(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        return 0;
    }

    /**
     * @param bool|array|string|null $directory
     * @param false|array $files
     * @return mixed
     * @throws \Laravel\Octane\Exceptions\DdException
     */
    public function getAllSongsFilesFromTheS3BucketMusicFolder(bool|array|string|null $directory, false|array $files): mixed
    {
// get all songs files from the s3 bucket music folder
        $uploadedSongs = Storage::disk('s3')->files($directory);
        $uploadedSongsCount = count($uploadedSongs);
        $this->info("Found {$uploadedSongsCount} songs in the s3 bucket");

        // Extract the slug from the songs in the s3 bucket and
        $uploadedSongsSlugs = [];
        foreach ($uploadedSongs as $uploadedSong) {
            $uploadedSongSlug = str_replace("$directory/", '', $uploadedSong);
            $uploadedSongSlug = str_replace('.mp3', '', $uploadedSongSlug);
            $uploadedSongSlug = str_replace('.jpg', '', $uploadedSongSlug);
            $uploadedSongSlug = str_replace('.jpeg', '', $uploadedSongSlug);

            $uploadedSongsSlugs[] = $uploadedSongSlug;
        }
        //check if they exist in the database
        $songs = Song::query()->get();
        $songsCount = $songs->count();

        $songsSlugs = $songs->pluck('slug')->toArray();
        // get the difference between the songs in the database and the songs in the s3 bucket
        $deletableSongs = array_diff($songsSlugs, $uploadedSongsSlugs);
        $deletableSongsCount = count($deletableSongs);


        // Get slugs from files
        $filesSlugs = [];
        foreach ($files as $file) {
            $fileSlug = basename($file);
            $fileSlug = str_replace('.mp3', '', $fileSlug);
            $fileSlug = str_replace('.jpg', '', $fileSlug);
            $fileSlug = str_replace('.jpeg', '', $fileSlug);
            $filesSlugs[] = $fileSlug;
        }

        // dd the first 5 of each
        dd([
            'uploadedSongsSlugs' => array_slice($uploadedSongsSlugs, 0, 5),
            'songsSlugs' => array_slice($songsSlugs, 0, 5),
            'deletableSongs' => array_slice($deletableSongs, 0, 5),
            'fileslugs' => $filesSlugs
        ]);
        return $file;
    }
}
