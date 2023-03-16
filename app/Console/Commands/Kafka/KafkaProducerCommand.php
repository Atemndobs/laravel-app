<?php

namespace App\Console\Commands\Kafka;

use App\Models\Song;
use Illuminate\Console\Command;

class KafkaProducerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kafka:produce {--t|topic=} {--i|id=}  {--s|slug=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kafka Producer';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $topic = $this->option('topic');
        $id = $this->option('id');
        $slug = $this->option('slug');
        if (empty($topic)) {
            $topic = 'songs';
        }

        if ($id) {
            $this->info("Searching Song for $id");
            $songToPublish = Song::query()->find($id);
        }else{
            $songToPublish = Song::query()->first();
        }
        $this->info('Testing Kafka...');
        $publisher = new \App\Services\Kafka\Publisher();

        $this->line('Publishing song: ' . $songToPublish->title);
        $publisher->publish($songToPublish->toJson(), $topic);
       // $publisher->produce();

    }
}
