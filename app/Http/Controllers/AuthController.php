<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // ── Superadmin B: Break-glass safety net ──
        $superBUser = env('SUPERADMIN_B_USERNAME');
        $superBPass = env('SUPERADMIN_B_PASSWORD');

        if ($superBPass && $request->username === $superBUser && $request->password === $superBPass) {
             // Sync the break-glass user to the database so Sanctum can issue a token
             $user = User::updateOrCreate(
                ['username' => $superBUser],
                [
                    'name' => 'Superadmin B',
                    'password' => $superBPass, // hashed by model cast
                    'role' => 'SUPERADMIN_B'
                ]
            );

            return response()->json([
                'user' => $user,
                'token' => $user->createToken('break_glass_token')->plainTextToken
            ]);
        }

        // ── Standard Login ──
        $user = User::where('username', $request->username)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['The provided credentials are incorrect.'],
            ]);
        }

        return response()->json([
            'user' => $user,
            'token' => $user->createToken('admin_token')->plainTextToken
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
