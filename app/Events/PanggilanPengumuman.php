<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PanggilanPengumuman implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $text;

    public function __construct($text)
    {
        $this->text = $text;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('antrian-channel'),
        ];
    }

    public function broadcastAs()
    {
        return 'panggilan-pengumuman';
    }
}
