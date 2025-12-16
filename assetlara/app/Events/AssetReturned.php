<?php

namespace App\Events;

use App\Models\Assignment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AssetReturned implements ShouldBroadcast 
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $assignment;

    public function __construct(Assignment $assignment)
    {
        $this->assignment = $assignment->load(['user', 'asset']); // Eager load for broadcast payload
    }

    // 🟢 Tell Laravel where to broadcast the data.
    public function broadcastOn(): Channel
    {
        return new Channel('dashboard');
    }

    // 🟢 What data to send to the frontend (Cleaner JSON)
    public function broadcastWith(): array
    {
        return [
            'asset_id' => $this->assignment->asset->id,
            'asset_name' => $this->assignment->asset->name,
            'user_name' => $this->assignment->user->name,
            'status' => 'available', 
            'time' => now()->toDateTimeString(),
        ];
    }

    // 🟢 Custom event name for frontend
    public function broadcastAs(): string
    {
        return 'AssetReturned';
    }
}
