<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; // Wajib Now
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CooldownTriggered implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $duration;

    public function __construct($duration = 30)
    {
        $this->duration = $duration; // Durasi detik
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('antrian-channel'), // Channel yang sama dengan antrian
        ];
    }

    public function broadcastAs()
    {
        return 'cooldown-started';
    }
}
