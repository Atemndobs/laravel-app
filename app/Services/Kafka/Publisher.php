<?php

namespace App\Services\Kafka;

use Faker\Provider\Uuid;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Producers\MessageBatch;
use Junges\Kafka\Message\Message;
use Ramsey\Uuid\Rfc4122\UuidV6;


class Publisher
{
    public function produce(string $topic='songs')
    {
        /** @var \Junges\Kafka\Producers\ProducerBuilder $producer */
        $producer = Kafka::publishOn($topic)
            ->withKafkaKey('kafka-key')
            ->withHeaders(['header-key' => 'header-value'])
        ;

        $producer->send();
    }

    /**
     * @throws \Exception
     */
    public function publish(string $rawMessage, string $topic='songs')
    {

        $message = new Message(
            $topic,
          //  headers: ['header-key' => 'header-value'],
            body: [
                'id' => Uuid::numerify('########-####-####-####-############'),
                'key' => $rawMessage
            ],
          //  key: 'kafka key here',
        );
        Kafka::publishOn($topic)->withMessage($message)->send();
        dump([
            'topic' => $topic,
            'message' => $message
        ]);
    }
}
