<?php

namespace App\Console\Commands\Markable;

use App\Models\Song;
use App\Models\User;
use App\Services\Markable\MarkableService;
use Illuminate\Console\Command;
use Illuminate\Support\Benchmark;
use Maize\Markable\Models\Like;

class MarkerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:mark {slug} {user_id?} {--t|type=} {--r|reaction=}';

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
        $slug = $slug = $this->argument('slug');
        $user_id = $this->argument('user_id') ?? 1 ;
        $type = $this->option('type') ?? 'Like';
        $reaction = $this->option('reaction') ?? 'heart';
        $marker = new MarkableService();

        $marker->markBySlug($slug, $type, $user_id, $reaction);

        dd([
            'slug' => $slug,
            'type' => $type,
            'user_id' => $user_id
        ]);

        return 0;
    }
}
