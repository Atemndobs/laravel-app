<?php

namespace App\Websockets\SocketHandler;

use BeyondCode\LaravelWebSockets\Apps\App;
use BeyondCode\LaravelWebSockets\QueryParameters;
use BeyondCode\LaravelWebSockets\WebSockets\Exceptions\UnknownAppKey;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Ratchet\ConnectionInterface;

abstract class BaseSocketHandler implements \Ratchet\WebSocket\MessageComponentInterface
{
    /**
     * {@inheritDoc}
     */
    public function onOpen(ConnectionInterface $conn)
    {
        $this->verifyAppKey($conn)->generateSocketId($conn);
        dump('Connection Opened');
    }

    /**
     * {@inheritDoc}
     */
    public function onClose(ConnectionInterface $conn)
    {
        dump('On CLOSE');
        return "Connection Closed";
    }

    /**
     * {@inheritDoc}
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        dump('On Error');
        $message = [
            'error' => [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ],
            'action' => 'websockets:error',
        ];

        Log::error(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        return response()->json([
            'error' => [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ],
        ]);
    }

    protected function generateSocketId(ConnectionInterface $connection)
    {
        $socketId = sprintf('%d.%d', random_int(1, 1000000000), random_int(1, 1000000000));

        $connection->socketId = $socketId;

        return $this;
    }

    protected function verifyAppKey(ConnectionInterface $connection)
    {
        $appKey = QueryParameters::create($connection->httpRequest)->get('appKey');
        if (! $app = App::findByKey($appKey)) {
            throw new UnknownAppKey($appKey);
        }

        $connection->app = $app;

        return $this;
    }
}
