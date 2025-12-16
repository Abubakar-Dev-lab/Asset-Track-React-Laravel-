<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetResource extends JsonResource
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
            'name' => $this->name,
            'serial_number' => $this->serial_number,
            'status' => $this->status,
            'category_id' => $this->category_id,
            'image_path' => $this->image_path,
            'image_url' => $this->image_path ? asset('storage/' . $this->image_path) : null,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            // Conditional relationships
            'category' => new CategoryResource($this->whenLoaded('category')),
            'assignments' => AssignmentResource::collection($this->whenLoaded('assignments')),
            // Current holder (active assignment)
            'current_holder' => new UserResource($this->whenLoaded('currentHolder')),
            // Active assignment for my-assets page
            'assigned_at' => $this->active_assignment?->assigned_at?->toISOString(),
        ];
    }
}
