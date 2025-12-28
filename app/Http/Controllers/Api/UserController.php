<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Get all users
     */
    public function index(): JsonResponse
    {
        $users = User::orderBy('created_at', 'desc')->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ];
        });

        return ApiResponse::success($users);
    }

    /**
     * Create a new user
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
        ]);

        return ApiResponse::created([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ], 'User created successfully');
    }

    /**
     * Update a user
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return ApiResponse::notFound("User not found");
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'password' => ['sometimes', 'confirmed', Password::min(8)],
        ]);

        $data = $request->only(['name', 'email']);
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return ApiResponse::success([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ], 'User updated successfully');
    }

    /**
     * Delete a user
     */
    public function destroy(int $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return ApiResponse::notFound("User not found");
        }

        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return ApiResponse::error('You cannot delete your own account', 403);
        }

        // Prevent deleting if it's the last admin user
        if (User::count() <= 1) {
            return ApiResponse::error('Cannot delete the last admin user', 403);
        }

        $user->delete();

        return ApiResponse::success(null, 'User deleted successfully');
    }
}
