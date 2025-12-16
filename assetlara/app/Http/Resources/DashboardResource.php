<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'stats' => [
                'total_assets' => $this->resource['total_assets'],
                'available_assets' => $this->resource['available_assets'],
                'assigned_assets' => $this->resource['assigned_assets'],
                'total_employees' => $this->resource['total_employees'],
                'total_categories' => $this->resource['total_categories'],
            ],
            'recent_assignments' => AssignmentResource::collection($this->resource['recent_assignments']),
        ];
    }
}
