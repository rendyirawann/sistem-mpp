<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; // Pakai Now agar instan
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PanggilanAntrian implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;

    public function __construct($data)
    {
        // $data berisi: no_antrian, nama_loket, dll
        $this->data = $data;
    }

    public function broadcastOn(): array
    {
        // Nama channel 'kios-channel' (bisa apa saja)
        return [
            new Channel('kios-channel'),
        ];
    }

    public function broadcastAs()
    {
        return 'PanggilanAntrian';
    }
}
