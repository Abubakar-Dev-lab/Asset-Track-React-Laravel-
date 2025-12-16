<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\CategoryService;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $categories = $this->categoryService->getFiltered(
            $request->only(['search', 'sort_by', 'sort_order']),
            $request->boolean('paginate', true),
            $request->get('per_page', 15)
        );

        return CategoryResource::collection($categories);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        $category = $this->categoryService->store($validated);

        return response()->json([
            'message' => 'Category created successfully',
            'category' => new CategoryResource($category),
        ], 201);
    }

    public function show(Category $category): JsonResponse
    {
        $category = $this->categoryService->getWithAssets($category);
        $category->loadCount('assets');

        return response()->json([
            'category' => new CategoryResource($category),
        ]);
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);

        $this->categoryService->update($category, $validated);

        return response()->json([
            'message' => 'Category updated successfully',
            'category' => new CategoryResource($category),
        ]);
    }

    public function destroy(Category $category): JsonResponse
    {
        if (!$this->categoryService->delete($category)) {
            return response()->json([
                'message' => 'Cannot delete a category that has assets. Please reassign or delete the assets first.',
            ], 422);
        }

        return response()->json([
            'message' => 'Category deleted successfully',
        ]);
    }
}
