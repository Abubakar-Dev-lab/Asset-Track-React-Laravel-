<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->only(['role', 'search', 'sort_by', 'sort_order']);
        if ($request->has('is_active')) {
            $filters['is_active'] = $request->boolean('is_active');
        }

        $users = $this->userService->getFiltered($filters, $request->get('per_page', 15));

        return UserResource::collection($users);
    }

    public function store(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->is_active ?? true;

        $user = $this->userService->store($data);

        return response()->json([
            'message' => 'User created successfully',
            'user' => new UserResource($user),
        ], 201);
    }

    public function show(User $user): JsonResponse
    {
        $user = $this->userService->getWithAssignments($user);
        $user->loadCount([
            'assignments',
            'assignments as active_assignments_count' => function ($q) {
                $q->whereNull('returned_at');
            }
        ]);

        return response()->json([
            'user' => new UserResource($user),
        ]);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['sometimes', 'required', 'in:admin,employee'],
            'is_active' => ['sometimes', 'required', 'boolean'],
            'password' => ['sometimes', 'required', 'string', 'min:8'],
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $this->userService->update($user, $validated);

        return response()->json([
            'message' => 'User updated successfully',
            'user' => new UserResource($user),
        ]);
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        $result = $this->userService->delete($user, $request->user()->id);

        if ($result !== true) {
            return response()->json([
                'message' => $result,
            ], 422);
        }

        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }

    public function employees(): AnonymousResourceCollection
    {
        $employees = $this->userService->getActiveEmployees();

        return UserResource::collection($employees);
    }
}
