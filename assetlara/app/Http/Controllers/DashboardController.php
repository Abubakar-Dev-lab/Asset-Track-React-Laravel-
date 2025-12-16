<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    public function index(): View
    {
        $stats = $this->dashboardService->getStats();
        $recentAssignments = $this->dashboardService->getRecentAssignments();

        return view('dashboard', [
            'totalAssets' => $stats['total_assets'],
            'availableAssets' => $stats['available_assets'],
            'assignedAssets' => $stats['assigned_assets'],
            'totalEmployees' => $stats['total_employees'],
            'totalCategories' => $stats['total_categories'],
            'recentAssignments' => $recentAssignments,
        ]);
    }
}
