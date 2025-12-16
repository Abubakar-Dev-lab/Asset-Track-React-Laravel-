<?php

namespace App\Http\Controllers\Api;

use App\Models\Asset;
use Illuminate\Http\Request;
use App\Services\AssetService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\AssetResource;
use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AssetController extends Controller
{
    public function __construct(
        protected AssetService $assetService
    ) {
        $this->authorizeResource(Asset::class, 'asset');
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $assets = $this->assetService->getFiltered(
            $request->only(['status', 'category_id', 'search', 'sort_by', 'sort_order']),
            $request->get('per_page', 15)
        );

        return AssetResource::collection($assets);
    }

    public function store(StoreAssetRequest $request): JsonResponse
    {
        $asset = $this->assetService->store(
            $request->validated(),
            $request->file('image')
        );
        $asset->load('category');

        return response()->json([
            'message' => 'Asset created successfully',
            'asset' => new AssetResource($asset),
        ], 201);
    }

    public function show(Asset $asset): JsonResponse
    {
        $asset->load(['category', 'assignments.user', 'assignments.admin', 'currentHolder']);

        return response()->json([
            'data' => new AssetResource($asset),
        ]);
    }

    public function update(UpdateAssetRequest $request, Asset $asset): JsonResponse
    {
        $this->assetService->update(
            $asset,
            $request->validated(),
            $request->file('image')
        );
        $asset->load('category');

        return response()->json([
            'message' => 'Asset updated successfully',
            'asset' => new AssetResource($asset),
        ]);
    }

    public function destroy(Asset $asset): JsonResponse
    {
        if (!$this->assetService->delete($asset)) {
            return response()->json([
                'message' => 'Cannot delete an asset that is currently assigned. Please return it first.',
            ], 422);
        }

        return response()->json([
            'message' => 'Asset deleted successfully',
        ]);
    }

    public function myAssets(Request $request): AnonymousResourceCollection
    {
        $assets = $this->assetService->getUserAssets($request->user());

        return AssetResource::collection($assets);
    }

    public function available(): AnonymousResourceCollection
    {
        $assets = $this->assetService->getAvailable();

        return AssetResource::collection($assets);
    }
}
