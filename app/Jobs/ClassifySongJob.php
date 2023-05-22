<?php

namespace App\Jobs;

use App\Services\MoodAnalysisService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ClassifySongJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $track;

    /**
     * @param  string  $track
     */
    public function __construct(string $track)
    {
        $this->queue = 'classify';
        $this->track = $track;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        Log::info('Pushing : ' . $this->track . 'to queue' . $this->queue);
        $slug = $this->track;
        (new MoodAnalysisService())->getAnalysis($slug);
    }
}
