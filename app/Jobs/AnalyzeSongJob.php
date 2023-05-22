<?php

namespace App\Jobs;

use App\Services\MoodAnalysisService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AnalyzeSongJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $title;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $title)
    {
        $this->queue = 'analyze';
        $this->title = $title;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('Pushing : ' . $this->title . 'to queue' . $this->queue);
        (new MoodAnalysisService())->getAnalysis($this->title);
    }
}
