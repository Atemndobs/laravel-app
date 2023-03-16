<?php

namespace App\Services\Kafka;

class Handler
{
    public function __invoke(\Junges\Kafka\Contracts\KafkaConsumerMessage $message){
        dump($message->getBody());
        return $message->getBody();
    }
}
