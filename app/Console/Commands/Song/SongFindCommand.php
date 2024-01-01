<?php

namespace App\Console\Commands\Song;

use App\Models\Song;
use App\Services\FindSongService;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class SongFindCommand extends Command
{
    const DEFAULT_MAX_RESULTS = 10;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:find {--s|slug=} {--b|bpm=} {--a|scale=} {--k|key=}
    {--g|genre=} {--t|title=} {--i|image=} {--o|output=} {--m|max=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        ray()->clearAll();
        $findService = new FindSongService();
        $output = $this->option('output');
        $max = $this->option('max');
        // 4 possible values for output : null, small, slim, full
        if ($output === null || $output === false || $output === 'small') {
            $output = 'small';
        }elseif ($output === 'slim') {
            $output = 'slim';
        }elseif ($output === 'full') {
            $output = 'full';
        }else {
            $output = 'small';
        }
        $max = $max === null ? 5 : $max;



        $allArgs = $this->options();
        $header = $findService::ATTRIBUTES;

        $allArgs = Arr::where($allArgs, function ($value, $key) {
            return $value !== null && $value !== false;
        });
        if (count($allArgs) === 0) {
            $this->info('No arguments provided');
            $this->info(count(Song::all()->toArray()).' songs found');
            return 0;
        }

        // display all input options and values in blue text
        $this->output->writeln('<fg=magenta>Input options and values:</>');
        $this->output->writeln('<fg=bright-yellow> output : '.$output.'</>');
        $this->output->writeln('<fg=bright-yellow> max : '.$max.'</>');
        foreach ($allArgs as $key => $value) {
            if ($key === 'output' || $key === 'max') {
                continue;
            }
            $this->output->writeln('<fg=bright-blue>'.$key.': '.$value.'</>');
        }

        $allArgs = Arr::except($allArgs, ['output']);
        $allArgs = Arr::except($allArgs, ['max']);

        $songs = [];
        if (count($allArgs) > 1) {
            $this->info(count($allArgs).' arguments : '.implode(', ', $allArgs).' provided');
            // find by multiple attributes
            $songs = $findService->findByMultipleAttributes($allArgs);
            if ( $max <= self::DEFAULT_MAX_RESULTS) {
                $songs = array_slice($songs, 0, $max);
            }
        } else {
            // find by single attribute
            foreach ($allArgs as $key => $value) {
                $this->info("$key: $value");
                $songs = $findService->{'findBy'.ucfirst($key)}($value);
                $songs = Arr::collapse($songs);

                if (count($songs) === 0) {
                    $this->info('No songs found');
                    return 0;
                }

                if (count($songs) === 1) {
                    $this->info('1 song found');
                    $this->prepareTable($songs, $header, $output);
                    return 0;
                }

                if ( $max <= self::DEFAULT_MAX_RESULTS) {
                    $songs = array_slice($songs, 0, $max);
                }else{
                    // display songs in chunks of 10
                    $this->info('Found '.count($songs).' songs');
                    $results = [];
                    foreach ($songs as $chunkId => $chunk) {
                        dump($chunkId);
                        foreach ($chunk as $song) {
                            $results[] = $song['title'].' by '.$song['slug'];
                        }
                        $results[] = 'Next 10 songs';
                        $choice = $this->choice('Please chose a song', $results, 0);

                        if ($choice === 'Next 10 songs') {
                            $results = [];
                            continue;
                        } else {
                            $this->info($choice);
                        }
                    }
                    $this->info('Listen to : '.$choice);
                }
            }
        }
        $this->prepareTable($songs, $header, $output);
        return 0;
    }

    public function prepareTable($songs, $header, $output)
    {
        if ($output === 'full') {
            $this->table($header, $songs);
            return 0;
        }
        $reducedHeader = ['id','author','title', 'slug', 'bpm', 'scale', 'key', 'genre'];

        foreach ($songs as $key => $value) {
            $songs[$key] = Arr::only($value, $reducedHeader );
        }

        if ($output === 'slim') {
            $reducedHeader = ['id','slug'];

            foreach ($songs as $key => $value) {
                $songs[$key] = Arr::only($value, $reducedHeader );
            }
        }

        $this->table($reducedHeader, $songs);
    }
}
