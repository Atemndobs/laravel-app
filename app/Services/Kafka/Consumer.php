<?php

namespace App\Services\Kafka;

use Carbon\Exceptions\Exception;
use Junges\Kafka\Facades\Kafka;

class Consumer
{
    /**
     * @throws Exception
     */
    public function consume(string $topic='songs')
    {
        dump([
            'topic' => $topic
        ]);
        $consumer = Kafka::createConsumer([$topic])
            ->withHandler(new Handler())
            ->withAutoCommit()
            ->build();
        $consumer->consume();
    }
}
