<?php

namespace App\Console\Commands\Kafka;

use App\Models\Song;
use Carbon\Exceptions\Exception;
use Illuminate\Console\Command;

class KafkaConsumerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kafka:consume  {--t|topic=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kafka Consumer';

    /**
     * Execute the console command.
     *
     * @return int
     * @throws Exception
     */
    public function handle()
    {
        $topic = $this->option('topic');
        $this->info('Consumer Kafka...');
        $consumer = new \App\Services\Kafka\Consumer();
        $consumer->consume($topic);

    }
}
