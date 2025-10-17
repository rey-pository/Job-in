<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Handle authentication for Admin, Corporate, and Jobseeker
     */
    public function login(Request $request)
    {
        // Validate input fields
        $credentials = $request->validate([
            'login' => 'required|string', // can be email or phone_number
            'password' => 'required|string',
        ]);

        // Find user by email or phone number
        $user = User::where('email', $credentials['login'])
                    ->orWhere('phone_number', $credentials['login'])
                    ->first();

        // Validate user existence and password hash
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['Invalid credentials.'],
            ]);
        }

        // Check corporate verification status
        if ($user->role_id == 2 && $user->status_verifikasi !== 'approved') {
            return response()->json([
                'message' => 'Corporate account is pending admin approval.'
            ], 403);
        }

        // Revoke old tokens before issuing a new one
        $user->tokens()->delete();

        // Generate Sanctum token for API access
        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                'role_id' => $user->role_id,
                'status_verifikasi' => $user->status_verifikasi,
            ]
        ], 200);
    }

    /**
     * Invalidate the active token and logout user
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout successful.'
        ], 200);
    }
}
