<?php

namespace App\Console\Commands\Song;

use App\Models\Song;
use App\Services\FindSongService;
use App\Services\Song\SongUploadService;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class SongUploadCommand extends Command
{
    const DEFAULT_MAX_RESULTS = 10;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:upload {--f|file} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Uploading files with  use of the CLI';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $file = $this->option('file');

        $songUploadService = new SongUploadService();

        dd($songUploadService->uploadSong($file));

        return 0;
    }
}
