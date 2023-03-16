<?php

namespace App\Console\Commands\Analysis;

use App\Models\Song;
use Illuminate\Console\Command;
use Illuminate\Support\Benchmark;

class CheckAnalyzedSongsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:status {--a|analyzed} {--s|status} {--t|total}';

    /**
     * The console command description
     *
     * @var string
     */
    protected $description = "Show pending songs for analysis or classification', [
        'analyzed' => 'Show analyzed songs',
        'status' => 'Show songs with status',
        'total' => 'analyze all  songs',
    ]";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $songs = Song::all();

        $analyzed_stats = [];
        $status_stats = [];
        $pending = [];
        if ($this->option('analyzed')) {
            $analyzed = $songs->where('analyzed', true);
            $pending = $songs->where('analyzed', false);
            $queued = $songs->where('status', '=', 'queued');
            $tot = $analyzed->count() + $pending->count();

            $analyzed_stats = [
                'analyzed' => $analyzed->count(),
                'no_analyzed' => $pending->count(),
                'queued' => $queued->count(),
                'total' => $tot,
            ];
        }
        if ($this->option('status')) {
            $status = $songs->where('status', '!=', 're-classified');
            $re_classified = $songs->where('status', '=', 're-classified');
            $deleted= $songs->where('status', '=', 'deleted');
            $queued = $songs->where('status', '=', 'queued');
            $tot =
                $re_classified->count()
                + $deleted->count()
                + $queued->count();

            $status_stats = [
                'not_yet_re-classified' => $status->count(),
                're-classified' => $re_classified->count(),
                'deleted' => $deleted->count(),
                'queued' => $queued->count(),
                'total' => $tot,
            ];

        }

        $tot = false;
        if ($this->option('total')) {
            $tot = $this->option('total');
            $this->analyzePending($pending, $tot);
        }

        if (sizeof($pending) > 3) {
            $tot = true;
        }

        if ($this->option('analyzed') && $this->option('status')) {
            $this->info('Analyzed Stats');
            $this->table(['analyzed', "<fg=magenta> not_analyzed </>",  "<fg=red> queued </>", 'total'], [$analyzed_stats]);
            $this->info('Status Stats');
            $this->table(['not_yet_re-classified', 're-classified', 'deleted', "<fg=red> queued </>",'total'], [$status_stats]);
            $this->analyzePending($pending, $tot);
        } elseif ($this->option('analyzed')) {
            $this->info('Analyzed Stats');
            $this->table(['analyzed', "<fg=magenta> not_analyzed </>",  "<fg=red> queued </>", 'total'], [$analyzed_stats]);
            $this->analyzePending($pending, $tot);
        } elseif ($this->option('status')) {
            $this->info('Status Stats');
            $this->table(['not_yet_re-classified', 're-classified', 'deleted', "<fg=red> queued </>",'total'], [$status_stats]);
        } else {
            $this->info('Total: ' . count($songs));
        }

        return 0;
    }

    public function analyzePending($pending, bool $tot = false)
    {
        $count = count($pending);
        if ($count == 0) {
            return;
        }
        if ($tot){

            foreach ($pending as $song) {
                $this->call('song:analyze', ['slug' => $song->slug]);
            }
            return;
        }

        if ($count < 3) {
            foreach ($pending as $song) {
                $this->call('song:analyze', ['slug' => $song->slug]);
            }
            return;
        }
        $ask = $this->askWithCompletion("analyse all $count songs ? ", ['y', 'n'], 'n');
        if ($ask === 'y') {
            foreach ($pending as $song) {
                $this->call('song:analyze', ['slug' => $song->slug]);
            }
            return;
        }
        /** @var Song $song */
        foreach ($pending as $song) {
            $ask = $this->askWithCompletion("analyse:  title: $song->title  | slug: $song->slug ? ", ['y', 'n'], 'n');
            if ($ask === 'y') {
                $this->call('song:analyze', ['slug' => $song->slug]);
            }
        }

    }
}
