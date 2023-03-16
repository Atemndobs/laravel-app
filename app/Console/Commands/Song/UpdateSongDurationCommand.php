<?php

namespace App\Console\Commands\Song;

use App\Services\SongUpdateService;
use Illuminate\Console\Command;

class UpdateSongDurationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:duration {slug?} {--f|field=null}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update song Duration description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $updateService = new SongUpdateService();
        $slug = $this->argument('slug');
        if ($slug !== null) {
            $this->info("prepare updating |  $slug");
            $results = $updateService->updateDuration($slug);
        }else{
            $results = $updateService->updateDuration();
        }

        try {
            $this->table(['title','author',  'duration', 'slug', 'image'], [$results]);
        }catch (\Exception $e){
            $this->info("Image From $slug");
            $this->table(['title','author',  'duration', 'slug', 'image'], $results);
        }
        return 0;
    }
}
