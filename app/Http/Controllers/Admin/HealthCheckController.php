<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;
use Spatie\CpuLoadHealthCheck\CpuLoadCheck;
use Spatie\Health\Checks\Checks\CacheCheck;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\Health;
use Spatie\Health\Http\Controllers\HealthCheckResultsController;
use Spatie\Health\ResultStores\ResultStore;

class HealthCheckController extends HealthCheckResultsController
{
    public function __invoke(Request $request, ResultStore $resultStore, Health $health): JsonResponse|View
    {
        if ($request->has('fresh')) {
            Artisan::call(RunHealthChecksCommand::class);
        }

        $checkResults = $resultStore->latestResults();
        return view('health::list', [
            'lastRanAt' => new Carbon($checkResults?->finishedAt),
            'checkResults' => $checkResults,
            'assets' => $health->assets(),
            'theme' => config('health.theme'),
        ]);
    }
}
