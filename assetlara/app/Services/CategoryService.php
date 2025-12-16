<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryService
{
    /**
     * Get paginated list of categories with asset counts.
     */
    public function getAll(int $perPage = 10): LengthAwarePaginator
    {
        return Category::withCount('assets')
            ->orderBy('name')
            ->paginate($perPage);
    }

    /**
     * Get filtered categories (for API).
     */
    public function getFiltered(array $filters = [], bool $paginate = true, int $perPage = 15): LengthAwarePaginator|Collection
    {
        $query = Category::withCount('assets');

        if (!empty($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }

        $sortBy = $filters['sort_by'] ?? 'name';
        $sortOrder = $filters['sort_order'] ?? 'asc';
        $query->orderBy($sortBy, $sortOrder);

        return $paginate ? $query->paginate($perPage) : $query->get();
    }

    /**
     * Get a category with its assets loaded.
     */
    public function getWithAssets(Category $category): Category
    {
        return $category->load('assets');
    }

    /**
     * Create a new category.
     */
    public function store(array $data): Category
    {
        return Category::create($data);
    }

    /**
     * Update a category.
     */
    public function update(Category $category, array $data): Category
    {
        $category->update($data);
        return $category;
    }

    /**
     * Delete a category.
     * Returns false if category has assets.
     */
    public function delete(Category $category): bool
    {
        if ($category->assets()->count() > 0) {
            return false;
        }

        return $category->delete();
    }
}
