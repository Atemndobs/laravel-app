<?php

namespace App\Console\Commands\Song;

use App\Models\Song;
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
        }

        // no slug is given so get all songs wth no duration and update them
        $songs = Song::query()->whereNull('duration')->get();
        $this->info("prepare updating |  {$songs->count()} songs");
        $this->withProgressBar($songs, function ($song) use ($updateService, &$results) {
            try {
                $this->line("");
                $this->info("Updating {$song->slug}");
                $results = $updateService->updateDuration($song->slug);
                $this->warn(json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            }catch (\Exception $e){
                $message = [
                    'error' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile(),
                ];
                $this->line("</fg=red>". json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ."</>");
            }
        });


        try {
            $this->table(['title','author',  'duration', 'slug', 'image'], [$results]);
        }catch (\Exception $e){
            $this->info("Image From $slug");
            $this->table(['title','author',  'duration', 'slug', 'image'], $results);
        }
        return 0;
    }
}
