<?php

namespace App\Console\Commands\Song;

use App\Services\Song\SongSyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SongSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $songSyncService = new SongSyncService();
        $songSyncService->getSongDiffs();
        $message = [
            'not in DB' => count($songSyncService->getSongsNotinDatabase()),
            'not in Storage' => count($songSyncService->getSongsNotinStorage()),
        ];
        Log::info(json_encode($message, JSON_PRETTY_PRINT));
        $bar = $this->output->createProgressBar(count($songSyncService->getSongsNotinDatabase()));
        foreach ($songSyncService->getSongsNotinDatabase() as $track) {
            try {
                $songSyncService->syncDbSingleSong($track);
            } catch (\Exception $e) {
                $this->error($e->getMessage());
                die();
            }
            $bar->advance();
        }
        $bar->finish();
        $this->info("Database sync complete for " . count($songSyncService->getSongsNotinDatabase()) . " songs");
        return 0;
    }
}
