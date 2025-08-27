<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;

class StatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public array $payload) {}

    public function broadcastOn(): Channel
    {
        return new Channel('status'); // public channel: 'status'
    }

    public function broadcastAs(): string
    {
        return 'status.updated';      // client listen('.status.updated')
    }

    public function broadcastWith(): array
    {
        return $this->payload;
    }
}
