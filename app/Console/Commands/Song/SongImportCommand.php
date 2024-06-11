<?php

namespace App\Console\Commands\Song;

use App\Jobs\ClassifySongJob;
use App\Models\Catalog;
use App\Models\Setting;
use App\Models\Song;
use App\Services\MoodAnalysisService;
use App\Services\SongUpdateService;
use App\Services\UploadService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use function example\int;

class SongImportCommand extends Command
{
    use Tools;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:import {source?} {--p|path=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Songs from storage/audio directory --source=local|cloud|other audio directory';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $source = $this->argument('source');
        $path = $this->option('path');
        $uploadService = new UploadService();
        $unClassified = [];
        $data = [];
        $audioFiles = glob('/var/www/html/storage/app/public/uploads/audio/*.mp3');
        $audioFiles = array_merge($audioFiles, glob('/var/www/html/storage/app/public/uploads/audio/*/*.mp3'));
        $audioFiles = array_merge($audioFiles, glob('/var/www/html/storage/app/public/uploads/audio/*/*/*.mp3'));
        $audioFiles = array_filter($audioFiles, 'is_file');
        $this->info('Found ' . count($audioFiles) . ' files');
        $this->call('move:audio');

        if ($path) {
            // if path does not start with "/var/www/html/" add it
            if (strpos($path, '/var/www/html/') === false) {
                $path = '/var/www/html/' . $path;
            }
            try {
                // check if file exists
                if (!File::exists($path)) {
                    $this->error("File does not exist: $path");
                    return 0;
                }
                $uploadService->uploadSong($path);
            } catch (\Exception $e) {
                $this->warn($e->getMessage());
                return 0;
            }
            $baseName = basename($path);
            $s3_base_url = Setting::query()->where('key', 'base_url')
                ->where('group', 's3')
                ->first()->value;
            $s3_path = $s3_base_url . '/music/' . $baseName;
            $message = json_encode([
                'message' => 'File uploaded & not deleted',
                'file' => $path,
                's3_path' => $s3_path,
                'Command' => 'song:import, line: ' . __LINE__,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            Log::info($message);
            $this->line("<fg=bright-magenta>". $message ."</>");
            return 0;
        }

        $this->output->progressStart(count($audioFiles));
        foreach ($audioFiles as $file) {
            $this->output->write("\n");
            try {
                $this->info('Uploading ' . $file);
                $uploadService->uploadSong($file);
            }catch (\Exception $e){
                $this->warn($e->getMessage());
                continue;
            }
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();

        try {
            $this->info('SKIPP PURGING QUEUE' );
//           $this->call('rabbitmq:queue-purge', ['queue' => 'classify']);
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

        $eventMessage = 'New song added';
        if (count($unClassified) > 0){
            $eventMessage = $unClassified;
        }
        event(new \App\Events\NewSongEvent($eventMessage));
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
