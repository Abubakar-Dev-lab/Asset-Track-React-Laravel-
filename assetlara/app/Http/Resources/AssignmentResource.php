<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'asset_id' => $this->asset_id,
            'user_id' => $this->user_id,
            'assigned_by' => $this->assigned_by,
            'assigned_at' => $this->assigned_at?->toISOString(),
            'returned_at' => $this->returned_at?->toISOString(),
            'notes' => $this->notes,
            'is_active' => is_null($this->returned_at),
            // Conditional relationships
            'asset' => new AssetResource($this->whenLoaded('asset')),
            'user' => new UserResource($this->whenLoaded('user')),
            'admin' => new UserResource($this->whenLoaded('admin')),
        ];
    }
}
