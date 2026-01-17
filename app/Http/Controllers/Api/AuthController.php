<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends ApiController
{
    /**
     * Login user and return token
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', $validator->errors()->toArray(), 422);
        }

        $user = User::where('username', $request->username)
            ->orWhere('email', $request->username)
            ->first();

        // Check if user exists and account is not locked
        if (!$user) {
            return $this->error('Invalid credentials', [], 401);
        }

        // Check if account is locked
        if ($user->locked_until && $user->locked_until->isFuture()) {
            $minutesRemaining = now()->diffInMinutes($user->locked_until);
            return $this->error("Account is locked. Please try again in {$minutesRemaining} minutes.", [], 423);
        }

        // Check if account is active
        if (!$user->is_active) {
            return $this->error('Account is deactivated. Please contact administrator.', [], 403);
        }

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            // Increment failed login attempts
            $user->increment('failed_login_attempts');
            
            // Lock account after 5 failed attempts
            if ($user->failed_login_attempts >= 5) {
                $user->locked_until = now()->addMinutes(30);
                $user->save();
                return $this->error('Account locked due to too many failed login attempts. Please try again in 30 minutes.', [], 423);
            }
            
            return $this->error('Invalid credentials', [], 401);
        }

        // Reset failed login attempts on successful login
        $user->failed_login_attempts = 0;
        $user->locked_until = null;
        $user->last_login = now();
        $user->save();

        // Create token
        $token = $user->createToken('auth-token')->plainTextToken;

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
            'token' => $token,
        ], 'Login successful');
    }

    /**
     * Get current authenticated user
     */
    public function me(Request $request): JsonResponse
    {
        return $this->success([
            'id' => $request->user()->id,
            'username' => $request->user()->username,
            'email' => $request->user()->email,
            'first_name' => $request->user()->first_name,
            'last_name' => $request->user()->last_name,
            'role' => $request->user()->role,
            'professional_title' => $request->user()->professional_title,
            'license_number' => $request->user()->license_number,
            'phone' => $request->user()->phone,
        ]);
    }

    /**
     * Logout user (revoke token)
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        
        return $this->success(null, 'Logged out successfully');
    }

    /**
     * Change password
     */
    public function changePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => [
                'required',
                'string',
                'min:12',
                'regex:/[a-z]/',      // lowercase
                'regex:/[A-Z]/',      // uppercase
                'regex:/[0-9]/',      // number
                'regex:/[^a-zA-Z0-9]/', // special character
            ],
        ], [
            'new_password.min' => 'Password must be at least 12 characters long.',
            'new_password.regex' => 'Password must contain uppercase, lowercase, number, and special character.',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', $validator->errors()->toArray(), 422);
        }

        $user = $request->user();

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return $this->error('Current password is incorrect', [], 401);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->password_changed_at = now();
        $user->save();

        return $this->success(null, 'Password changed successfully');
    }

    /**
     * Request password reset
     */
    public function requestPasswordReset(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', $validator->errors()->toArray(), 422);
        }

        $user = User::where('email', $request->email)->first();

        // Don't reveal if email exists (security)
        if (!$user) {
            // Still return success to prevent email enumeration
            return $this->success(null, 'If the email exists, a password reset link has been sent.');
        }

        // TODO: Implement email sending for password reset
        // For MVP, we'll just return success
        // In production, send email with reset token

        return $this->success(null, 'If the email exists, a password reset link has been sent.');
    }
}
