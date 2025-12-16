<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    /**
     * Get paginated list of users.
     */
    public function getAll(int $perPage = 15): LengthAwarePaginator
    {
        return User::orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Get filtered users (for API).
     */
    public function getFiltered(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = User::query();

        if (!empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $query->withCount([
            'assignments',
            'assignments as active_assignments_count' => function ($q) {
                $q->whereNull('returned_at');
            }
        ]);

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * Get active employees (for dropdowns).
     */
    public function getActiveEmployees(): Collection
    {
        return User::where('role', 'employee')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get a user with assignments loaded.
     */
    public function getWithAssignments(User $user): User
    {
        return $user->load('assignments.asset');
    }

    /**
     * Create a new user (admin creation).
     */
    public function store(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? 'employee',
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    /**
     * Update a user.
     */
    public function update(User $user, array $data): User
    {
        $user->update($data);
        return $user;
    }

    /**
     * Delete a user.
     * Returns error message string if deletion not allowed, true on success.
     */
    public function delete(User $user, int $currentUserId): bool|string
    {
        // Prevent self-deletion
        if ($user->id === $currentUserId) {
            return 'You cannot delete your own account!';
        }

        // Check for active assignments
        $activeAssignments = $user->assignments()->whereNull('returned_at')->count();
        if ($activeAssignments > 0) {
            return "Cannot delete user with $activeAssignments active asset assignment(s). Please return assets first.";
        }

        $user->delete();
        return true;
    }
}
