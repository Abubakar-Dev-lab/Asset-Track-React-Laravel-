<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AssignmentResource;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    public function index(): JsonResponse
    {
        $data = $this->dashboardService->getDashboardData();

        return response()->json([
            'stats' => $data['stats'],
            'recent_assignments' => AssignmentResource::collection($data['recent_assignments']),
            'assets_by_category' => $data['assets_by_category'],
            'assets_by_status' => $data['assets_by_status'],
        ]);
    }
}
