<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; // WAJIB NOW AGAR CEPAT
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AntrianBaru implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $pesan;

    public function __construct()
    {
        $this->pesan = 'Ada antrian baru masuk';
    }

    public function broadcastOn(): array
    {
        // Pastikan nama channel SAMA PERSIS dengan channel panggilan
        return [
            new Channel('antrian-channel'),
        ];
    }

    public function broadcastAs()
    {
        // Nama event yang akan didengar javascript
        return 'antrian-baru';
    }
}
