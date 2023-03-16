<?php

namespace App\Console\Commands\Analysis;

use App\Models\Song;
use App\Services\MoodAnalysisService;
use Illuminate\Console\Command;

class AnalyzeSongCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:analyze {slug}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analyze / Classify Single Song';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $slug = $this->argument('slug');
        (new MoodAnalysisService())->getAnalysis($slug);

        $this->output->info("$slug : analysis is in progress");
        $this->info('job is has been queued');

        return 0;
    }
}
