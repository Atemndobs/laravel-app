<?php

namespace App\Console\Commands\Analysis;

use App\Models\Song;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ClearClassifierCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:clean {table?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean the Classifier Audio Directory';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $deleteItems = [];
        $deleteItems[] = $this->cleanSongDb();
        $url = 'http://host.docker.internal:3000/music/delete';
        $ask = $this->ask('Are you sure you want to delete all songs?', 'yes');
        if ($ask === 'no') {
            $this->error('Song Delete Aborted');
            return 1;
        }

        try {
            $response = Http::get($url)->body();
        }catch (\Exception $e) {
            $response = 'Error: ' . $e->getMessage();
            $this->error($response);
            $this->info('Please run the following command manually first then try again:');
           // $this->line('<fg=blue>' . 'cd ~/sites/curator/music-player && npm run serve --fix &' . '</>'); //  => alias fe
            $this->line('<fg=blue>' . 'cd ~/sites/curator/nested && npm run start:dev &' . '</>'); // ex
            $this->info('OR simple run the alias: ex');
            return 1;
        }

        $deleteItems[] = $response;
        $this->output->info('Audio directory Cleaned');

        dump($deleteItems);

        return $response;
    }

    public function cleanSongDb()
    {
        $allSongs = Song::all();

        $fails = [];
        $pass = [];
        /** @var Song $song */
        foreach ($allSongs as $song) {
            $base = $song->path;
            $local = 'http://localhost:8899/';
            $url = str_replace('mage.tech', 'localhost', $base);

            try {
                $status = Http::get($url)->status();
                if ($status !== 200) {
                    $fails[$song->id] = $song->title;

                    $path = str_replace('http://mage.tech:8899/', '', $song->path);
                    $path = substr($path, 7);
                    $checkPath = Storage::get('public'.$path);
                    if ($checkPath === null) {
                        $song->delete();
                    }
                }
            } catch (\Exception $exception) {
                info($exception->getMessage());
            }
        }

        return $fails;
    }
}
