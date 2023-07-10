<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NewSongEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct($message)
    {
        $this->message = $message;
        $logMessage = [
            'Description' => 'info : Event Fired',
            'event' => 'NewSongEvent',
            'message' => $this->message,
            'time' => now()
        ];
        Log::info(json_encode($logMessage, JSON_PRETTY_PRINT));
    }

    public function broadcastOn()
    {

        return ['public'];
      //  return new Channel('public');
    }

    public function broadcastAs()
    {
        return 'NewSongEvent';
    }

    //   public function broadcastOn()
    //  {
    //      return ['my-channel'];
    //  }
    //
    //  public function broadcastAs()
    //  {
    //      return 'my-event';
    //  }
}
