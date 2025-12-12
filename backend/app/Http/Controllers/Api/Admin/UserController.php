<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\ApiController;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends ApiController
{
    /**
     * Display a listing of users
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::query();

        // Filter by role if provided
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        // Filter by active status if provided
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Search by name, username, or email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        return $this->paginated($users, 'Users retrieved successfully');
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:12',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[^a-zA-Z0-9]/',
            ],
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'role' => 'required|in:admin,clinician',
            'professional_title' => 'nullable|string|max:20',
            'license_number' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
        ], [
            'password.min' => 'Password must be at least 12 characters long.',
            'password.regex' => 'Password must contain uppercase, lowercase, number, and special character.',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', $validator->errors()->toArray(), 422);
        }

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'role' => $request->role,
            'professional_title' => $request->professional_title,
            'license_number' => $request->license_number,
            'phone' => $request->phone,
            'is_active' => true,
            'created_by' => $request->user()->id,
            'password_changed_at' => null, // Force password change on first login
        ]);

        return $this->success([
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'role' => $user->role,
                'professional_title' => $user->professional_title,
            ],
        ], 'User created successfully', 201);
    }

    /**
     * Display the specified user
     */
    public function show(User $user): JsonResponse
    {
        return $this->success([
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'role' => $user->role,
            'professional_title' => $user->professional_title,
            'license_number' => $user->license_number,
            'phone' => $user->phone,
            'is_active' => $user->is_active,
            'last_login' => $user->last_login,
            'failed_login_attempts' => $user->failed_login_attempts,
            'locked_until' => $user->locked_until,
            'created_at' => $user->created_at,
        ]);
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'username' => 'sometimes|string|max:50|unique:users,username,' . $user->id,
            'email' => 'sometimes|email|max:100|unique:users,email,' . $user->id,
            'first_name' => 'sometimes|string|max:50',
            'last_name' => 'sometimes|string|max:50',
            'role' => 'sometimes|in:admin,clinician',
            'professional_title' => 'nullable|string|max:20',
            'license_number' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', $validator->errors()->toArray(), 422);
        }

        $user->update($request->only([
            'username',
            'email',
            'first_name',
            'last_name',
            'role',
            'professional_title',
            'license_number',
            'phone',
            'is_active',
        ]));

        return $this->success([
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'role' => $user->role,
                'professional_title' => $user->professional_title,
                'is_active' => $user->is_active,
            ],
        ], 'User updated successfully');
    }

    /**
     * Remove the specified user (soft delete - deactivate)
     */
    public function destroy(User $user): JsonResponse
    {
        // Prevent deleting the last admin
        if ($user->role === 'admin') {
            $adminCount = User::where('role', 'admin')->where('is_active', true)->count();
            if ($adminCount <= 1) {
                return $this->error('Cannot deactivate the last admin user', [], 422);
            }
        }

        $user->is_active = false;
        $user->save();

        // Revoke all tokens
        $user->tokens()->delete();

        return $this->success(null, 'User deactivated successfully');
    }

    /**
     * Reset user password (admin-initiated)
     */
    public function resetPassword(Request $request, User $user): JsonResponse
    {
        // Generate temporary password
        $tempPassword = Str::random(16);

        $user->password = Hash::make($tempPassword);
        $user->password_changed_at = null; // Force change on next login
        $user->failed_login_attempts = 0;
        $user->locked_until = null;
        $user->save();

        // TODO: Send email with temporary password
        // For MVP, return it in response (not recommended for production)

        return $this->success([
            'temporary_password' => $tempPassword, // Remove in production, send via email
        ], 'Password reset successfully. User must change password on next login.');
    }

    /**
     * Unlock user account
     */
    public function unlock(User $user): JsonResponse
    {
        $user->failed_login_attempts = 0;
        $user->locked_until = null;
        $user->save();

        return $this->success(null, 'Account unlocked successfully');
    }

    /**
     * Get user activity logs
     */
    public function activity(User $user): JsonResponse
    {
        // TODO: Implement audit log retrieval
        // For now, return empty array
        return $this->success([
            'logs' => [],
            'message' => 'Activity logs will be available after audit logging is implemented',
        ]);
    }
}
