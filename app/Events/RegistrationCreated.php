<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class RegistrationCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public array $payload) {}

    public function broadcastOn(): Channel
    {
        return new Channel('registrations');
    }

    public function broadcastAs(): string
    {
        return 'registration.created';
    }

    public function broadcastWith(): array
    {
        return $this->payload;
    }
}
