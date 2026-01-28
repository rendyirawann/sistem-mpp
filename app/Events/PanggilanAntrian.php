<?php

namespace App\Events;

use App\Models\Antrian;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast; // 🔥 PENTING
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; // 🔥 Agar instan
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// Tambahkan "implements ShouldBroadcastNow" agar dikirim detik itu juga
class PanggilanAntrian implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;

    /**
     * Terima data antrian yang mau dikirim
     */
    public function __construct($antrianData)
    {
        $this->data = $antrianData;
    }

    /**
     * Nama Channel (Radio Frekuensi)
     */
    public function broadcastOn(): array
    {
        // Channel public agar semua Kios bisa dengar
        return [
            new Channel('antrian-channel'),
        ];
    }

    /**
     * Nama Event
     */
    public function broadcastAs()
    {
        return 'panggilan-baru';
    }
}
