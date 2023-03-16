<?php

namespace App\Console\Commands\FileWatcher;

use App\Services\UploadService;
use Illuminate\Console\Command;
use Illuminated\Console\WithoutOverlapping;
use Spatie\Watcher\Watch;

class AudioFileWatcherCommand extends Command
{
    use
        WithoutOverlapping,
        Tools;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'watch:audio';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Watch Audio Directory for New Files ';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Watching Audio Directory for New Files...');
        $destination = 'audio';

        $dir = storage_path('app/public/audio');
        $this->watchFolder($dir, $destination);
        return 0;
    }
}
