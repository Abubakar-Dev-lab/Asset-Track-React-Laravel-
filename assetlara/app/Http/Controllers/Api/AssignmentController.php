<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignAssetRequest;
use App\Http\Resources\AssignmentResource;
use App\Models\Asset;
use App\Models\Assignment;
use App\Services\AssignmentService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AssignmentController extends Controller
{
    protected AssignmentService $service;

    public function __construct(AssignmentService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of assignments.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Assignment::with(['asset', 'user', 'admin']);

        // Filter by active/returned
        if ($request->has('active')) {
            if ($request->boolean('active')) {
                $query->whereNull('returned_at');
            } else {
                $query->whereNotNull('returned_at');
            }
        }

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by asset
        if ($request->has('asset_id')) {
            $query->where('asset_id', $request->asset_id);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'assigned_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $assignments = $query->paginate($perPage);

        return AssignmentResource::collection($assignments);
    }

    /**
     * Display the specified assignment.
     */
    public function show(Assignment $assignment): JsonResponse
    {
        $assignment->load(['asset', 'user', 'admin']);

        return response()->json([
            'assignment' => new AssignmentResource($assignment),
        ]);
    }

    /**
     * Assign asset to user (Check-Out).
     */
    public function assign(AssignAssetRequest $request, Asset $asset): JsonResponse
    {
        try {
            $assignment = $this->service->assignAsset($asset, $request->user_id);
            $assignment->load(['asset', 'user', 'admin']);

            return response()->json([
                'message' => "Asset {$asset->name} assigned successfully",
                'assignment' => new AssignmentResource($assignment),
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Assignment failed: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Return asset (Check-In).
     */
    public function return(Asset $asset): JsonResponse
    {
        try {
            $assignment = $this->service->returnAsset($asset);
            $assignment->load(['asset', 'user', 'admin']);

            return response()->json([
                'message' => "Asset {$asset->name} returned successfully",
                'assignment' => new AssignmentResource($assignment),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Return failed: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get recent assignments (for dashboard).
     */
    public function recent(Request $request): AnonymousResourceCollection
    {
        $limit = $request->get('limit', 5);

        $assignments = Assignment::with(['asset', 'user', 'admin'])
            ->orderBy('assigned_at', 'desc')
            ->limit($limit)
            ->get();

        return AssignmentResource::collection($assignments);
    }
}
