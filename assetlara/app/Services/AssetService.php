<?php

namespace App\Services;

use App\Models\User;
use App\Models\Asset;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class AssetService
{
    /**
     * Get filtered and paginated assets.
     */
    public function getFiltered(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Asset::with('category');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * Get available assets for assignment.
     */
    public function getAvailable(): Collection
    {
        return Asset::where('status', 'available')
            ->with('category')
            ->orderBy('name')
            ->get();
    }

    /**
     * Create a new asset with optional image upload.
     */
    public function store(array $data, ?UploadedFile $image = null): Asset
    {
        if ($image) {
            $data['image_path'] = $image->store('assets', 'public');
        }

        return Asset::create($data);
    }

    /**
     * Update an asset with optional image replacement.
     */
    public function update(Asset $asset, array $data, ?UploadedFile $image = null): Asset
    {
        if ($image) {
            $this->deleteImage($asset);
            $data['image_path'] = $image->store('assets', 'public');
        }

        $asset->update($data);

        return $asset;
    }

    /**
     * Delete an asset (soft delete).
     * Returns false if asset is currently assigned.
     */
    public function delete(Asset $asset): bool
    {
        if ($asset->status === 'assigned') {
            return false;
        }

        return $asset->delete();
    }

    /**
     * Get all active assets assigned to a user.
     */
    public function getUserAssets(User $user): Collection
    {
        return $user->assignments()
            ->whereNull('returned_at')
            ->with(['asset.category', 'admin'])
            ->get()
            ->map(function ($assignment) {
                $asset = $assignment->asset;
                $asset->active_assignment = $assignment;
                return $asset;
            });
    }

    /**
     * Delete the asset's image from storage.
     */
    protected function deleteImage(Asset $asset): void
    {
        if ($asset->image_path && Storage::disk('public')->exists($asset->image_path)) {
            Storage::disk('public')->delete($asset->image_path);
        }
    }
}
