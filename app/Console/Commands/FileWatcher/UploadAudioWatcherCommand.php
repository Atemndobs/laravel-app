<?php

namespace App\Console\Commands\FileWatcher;

use App\Services\UploadService;
use Illuminate\Console\Command;
use Illuminated\Console\WithoutOverlapping;
use Spatie\Watcher\Watch;

class UploadAudioWatcherCommand extends Command
{
    use
        WithoutOverlapping,
        Tools;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'watch:upload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Watching the Upload Directory  [app/public/uploads/audio] for New Files';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Watching Audio Directory [app/public/uploads/audio] ');
        $dir = storage_path('app/public/uploads/audio');
        $destination = 'audio';
        $this->watchFolder($dir, $destination);
        return 0;
    }
}
