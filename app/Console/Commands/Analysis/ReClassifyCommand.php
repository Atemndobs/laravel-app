<?php

namespace App\Console\Commands\Analysis;

use App\Exceptions\SongException\NotAnalyzedException;
use App\Models\Song;
use App\Services\ClassifyService;
use Illuminate\Console\Command;

class ReClassifyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:reclassify {slug?} {--all}';

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
        $slug = $this->argument('slug');
        $all = $this->option('all');
        $classifyService = new ClassifyService();
        // if slug is empty, write console info : no slug given, all songs will be reclassified

        // start progress bar

        $songs = Song::query()->where(['analyzed' => null])->orWhere('status', '!=', 're-classified')->get();

        $results = [];
        if (empty($slug) || $slug == null || $slug == 'null') {
            $this->info('No slug given, all songs will be reclassified');
            if ($all) {
                $this->info('All songs will be reclassified');

                $this->output->progressStart(count($songs));
                foreach ($songs as $song) {
                    if ($song->status === 're-classified') {
                        continue;
                    }
                    $result = $classifyService->buildClassificaton($song);
                    $results[] = $result;
                    $this->output->progressAdvance();

                }
                $this->table(['slug', 'title', 'bpm', 'key', 'energy', 'scale'], $results);
                return 0;
            }
            $answer = $this->ask('Are you sure you want to reclassify all songs? (y/n)');
            if ($answer != 'y') {
                $this->error('Aborting');
                return 1;
            }
            $this->output->progressStart(count($songs));
            foreach ($songs as $song) {
                $result = $classifyService->buildClassificaton($song);
                $results[] = $result;
                $this->output->progressAdvance();

            }
            $this->table(['slug', 'title', 'bpm', 'key', 'energy', 'scale'], $results);
            return 0;

        } else {
            $this->info('Reclassifying song with slug: '.$slug);
            return $this->classify($classifyService, $slug);
        }

        // create table with results
        return 0;
    }

    /**
     * @param  ClassifyService  $classifyService
     * @param  array|string|null  $slug
     * @return int
     */
    public function classify(ClassifyService $classifyService, array|string|null $slug): int
    {
        $bar = $this->output->createProgressBar(100);
        $bar->start();
        $this->newLine();

        try {
            $classifiedSongs = $classifyService->reClassify($slug);
            $this->table(['slug', 'classification_properties', 'values'], $classifiedSongs);
            $this->newLine();
            $bar->finish();

            return 0;
        } catch (NotAnalyzedException $e) {
            $this->error($e->getMessage());
            $this->table(['slug', 'message'], [
                [$slug, $e->getMessage()],
            ]);

            return 1;
        }
    }
}
