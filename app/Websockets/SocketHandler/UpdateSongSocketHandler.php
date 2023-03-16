<?php

namespace App\Websockets\SocketHandler;

use Ratchet\ConnectionInterface;
use Ratchet\RFC6455\Messaging\MessageInterface;

class UpdateSongSocketHandler extends BaseSocketHandler
{
    public function onMessage(ConnectionInterface $conn, MessageInterface $msg)
    {
        dump(['received_message_from_fe:' => $msg->getPayload()]);
    }
}
