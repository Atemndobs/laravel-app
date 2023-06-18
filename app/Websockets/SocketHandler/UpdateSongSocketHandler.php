<?php

namespace App\Websockets\SocketHandler;

use Ratchet\ConnectionInterface;
use Ratchet\RFC6455\Messaging\MessageInterface;

class UpdateSongSocketHandler extends BaseSocketHandler
{
    public function onOpen(ConnectionInterface $connection)
    {
        $this->connections->add($connection);
    }

    public function onMessage(ConnectionInterface $from, MessageInterface $msg)
    {
        $this->connections->broadcast($msg);
    }

    public function onClose(ConnectionInterface $connection)
    {
        $this->connections->remove($connection);
    }

    public function onError(ConnectionInterface $connection, \Exception $e)
    {
        $connection->close();
    }
}
