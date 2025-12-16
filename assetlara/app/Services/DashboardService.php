<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\User;
use App\Models\Category;
use App\Models\Assignment;
use Illuminate\Support\Collection;

class DashboardService
{
    /**
     * Get basic dashboard statistics.
     */
    public function getStats(): array
    {
        return [
            'total_assets' => Asset::count(),
            'available_assets' => Asset::where('status', 'available')->count(),
            'assigned_assets' => Asset::where('status', 'assigned')->count(),
            'broken_assets' => Asset::where('status', 'broken')->count(),
            'maintenance_assets' => Asset::where('status', 'maintenance')->count(),
            'total_employees' => User::where('role', 'employee')->where('is_active', true)->count(),
            'total_admins' => User::where('role', 'admin')->where('is_active', true)->count(),
            'total_categories' => Category::count(),
            'active_assignments' => Assignment::whereNull('returned_at')->count(),
        ];
    }

    /**
     * Get recent assignments.
     */
    public function getRecentAssignments(int $limit = 5): Collection
    {
        return Assignment::with(['asset', 'user', 'admin'])
            ->orderBy('assigned_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get assets grouped by category.
     */
    public function getAssetsByCategory(): Collection
    {
        return Category::withCount('assets')
            ->orderBy('assets_count', 'desc')
            ->get()
            ->map(fn($cat) => [
                'id' => $cat->id,
                'name' => $cat->name,
                'count' => $cat->assets_count,
            ]);
    }

    /**
     * Get assets grouped by status.
     */
    public function getAssetsByStatus(): Collection
    {
        return Asset::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');
    }

    /**
     * Get all dashboard data in one call.
     */
    public function getDashboardData(): array
    {
        return [
            'stats' => $this->getStats(),
            'recent_assignments' => $this->getRecentAssignments(),
            'assets_by_category' => $this->getAssetsByCategory(),
            'assets_by_status' => $this->getAssetsByStatus(),
        ];
    }
}
