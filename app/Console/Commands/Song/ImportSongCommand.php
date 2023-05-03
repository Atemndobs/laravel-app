<?php

namespace App\Console\Commands\Song;

use App\Jobs\ClassifySongJob;
use App\Models\Catalog;
use App\Models\Song;
use App\Services\MoodAnalysisService;
use App\Services\SongUpdateService;
use App\Services\UploadService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use function example\int;

class ImportSongCommand extends Command
{
    use Tools;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:import {source?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Songs from storage/audio directory --source=local|rclone|other audio directory';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $source = $this->argument('source');
        $unClassified = [];
        $data = [];
        $audioFiles = glob('storage/app/public/uploads/audio/*.mp3');
        $this->info('Found ' . count($audioFiles) . ' files');

        // call move audio command
        $this->call('move:audio');

        $uploadService = new UploadService();
        $this->output->progressStart(count($audioFiles));
        foreach ($audioFiles as $file) {
            $this->output->write("\n");
            try {
                $this->info('Uploading ' . $file);
                $uploadService->uploadSong($file);
            }catch (\Exception $e){
                $this->warn($e->getMessage());
            }
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();

        try {
            $this->info('Purging queue classify' );
            $this->call('rabbitmq:queue-purge', ['queue' => 'classify']);
        }catch (\Exception $e){
           $this->warn('No classify queue to purge');
        }
        $queuedSongs = $this->classifySongs();


        foreach ($queuedSongs as $title) {
            $unClassified[] = $title;
            $data[] = [
                'num' => count($data) + 1,
                'title' => $title,
                'status' => 'imported',
            ];
            info("$title : has been imported");
        }

        $headers = [
            'num',
            'title',
            'status',
        ];
        $this->output->table($headers, $data);
        $this->info('Unclassified songs:');
        $total = count($unClassified);
        $this->output->info("imported $total songs from $source");

//        info('=========================================IMPORT_DONE==========================================');
//        info('Updating BPMs');
//        $this->call('song:bpm');
//        info('=========================================BPMs_DONE==========================================');
//        $this->call('song:status', ['--analyzed' => true, '--status' => true, '--total' => true]);

        return 0;
    }
    /**
     * @return array
     */
    public function classifySongs(): array
    {
        $songs = Song::query()->where('analyzed', '!=', 1)
            ->orWhereNull('analyzed')
            ->get();
        $unClassified = [];
        $bar = $this->output->createProgressBar(count($songs));
        $bar->start();
        /** @var Song $song */
        foreach ($songs as $song) {
            $bar->advance();
            if ($song->analyzed || $song->analyzed === 1) {
                $song->status = 'analyzed';
                continue;
            }

            if ($song->slug !== null){
                $song->status = 'queued';
                $song->save();
                $unClassified[] = $song->slug;
                ClassifySongJob::dispatch($song->slug);
            }

        }

        $bar->finish();
        return $unClassified;
    }

    /**
     * @return array
     */
    public function getDeleted()
    {
        $songs = Song::all();
        $deleted = [];
        foreach ($songs as $song) {
            if ($song->status === 'deleted') {
                $deleted[] = $song->slug;
             //   $song->delete();
            }
        }
        return $deleted;
    }
}
