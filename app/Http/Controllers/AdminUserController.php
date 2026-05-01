<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function list(Request $request)
    {
        $currentUser = $request->user();
        $query = User::select('id', 'name', 'username', 'role', 'created_at', 'updated_at');

        // Hide Superadmin B from Superadmin A and regular Admins
        if ($currentUser->role === 'SUPERADMIN') {
            $query->where('role', '!=', 'SUPERADMIN_B');
        } elseif ($currentUser->role === 'ADMIN') {
            // Regular admins probably shouldn't see anyone but themselves or others? 
            // The user only asked to hide B from A.
            $query->where('role', '!=', 'SUPERADMIN_B');
        }

        return $query->orderBy('created_at')->get();
    }

    public function create(Request $request)
    {
        $currentUser = $request->user();
        
        $request->validate([
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:6',
            'name'     => 'required|string',
            'role'     => 'required|in:ADMIN,SUPERADMIN,SUPERADMIN_B',
        ]);

        // Permission Hierarchy:
        // 1. Superadmin B can create anyone.
        // 2. Superadmin A can only create ADMIN.
        
        if ($currentUser->role === 'SUPERADMIN') {
            if ($request->role !== 'ADMIN') {
                return response()->json(['message' => 'Unauthorized: Superadmin A can only create regular Admins'], 403);
            }
        } elseif ($currentUser->role !== 'SUPERADMIN_B') {
            return response()->json(['message' => 'Unauthorized: Only Superadmins can create users'], 403);
        }

        $user = User::create([
            'username' => $request->username,
            'password' => $request->password,
            'name'     => $request->name,
            'role'     => $request->role,
        ]);

        return response()->json($user, 201);
    }

    public function update(Request $request)
    {
        $currentUser = $request->user();
        
        $request->validate([
            'userId'   => 'required|integer|exists:users,id',
            'name'     => 'nullable|string',
            'username' => 'required|string|unique:users,username,' . $request->userId,
        ]);

        $user = User::findOrFail($request->userId);
        
        // Permission Hierarchy:
        // 1. Superadmin B can edit anyone.
        // 2. Superadmin A can only edit ADMIN.
        
        if ($currentUser->role === 'SUPERADMIN') {
            if ($user->role !== 'ADMIN') {
                return response()->json(['message' => 'Unauthorized: Superadmin A can only edit regular Admins'], 403);
            }
        } elseif ($currentUser->role !== 'SUPERADMIN_B') {
             // A regular ADMIN cannot edit others
             if ($user->id !== $currentUser->id) {
                return response()->json(['message' => 'Unauthorized: You can only edit your own profile'], 403);
             }
        }

        $user->update([
            'name'     => $request->name ?? $user->name,
            'username' => $request->username,
        ]);

        return response()->json($user);
    }

    public function resetPassword(Request $request)
    {
        $currentUser = $request->user();

        $request->validate([
            'userId'      => 'required|integer|exists:users,id',
            'newPassword' => 'required|string|min:6',
        ]);

        $user = User::findOrFail($request->userId);
        
        // Permission Hierarchy:
        // 1. Superadmin B can reset anyone's password.
        // 2. Superadmin A can only reset regular ADMIN passwords.
        
        if ($currentUser->role === 'SUPERADMIN') {
            if ($user->role !== 'ADMIN') {
                return response()->json(['message' => 'Unauthorized: Superadmin A can only reset regular Admin passwords'], 403);
            }
        } elseif ($currentUser->role !== 'SUPERADMIN_B') {
            return response()->json(['message' => 'Unauthorized: Only Superadmins can reset passwords'], 403);
        }

        $user->update(['password' => $request->newPassword]);

        return response()->json(['message' => 'Password reset successfully']);
    }

    public function delete(Request $request)
    {
        $currentUser = $request->user();

        $request->validate([
            'userId' => 'required|integer|exists:users,id',
        ]);

        $user = User::findOrFail($request->userId);
        
        // Permission Hierarchy:
        // 1. Superadmin B can delete anyone.
        // 2. Superadmin A can only delete regular ADMINs.
        
        if ($currentUser->role === 'SUPERADMIN') {
            if ($user->role !== 'ADMIN') {
                return response()->json(['message' => 'Unauthorized: Superadmin A can only delete regular Admins'], 403);
            }
        } elseif ($currentUser->role !== 'SUPERADMIN_B') {
            return response()->json(['message' => 'Unauthorized: Only Superadmins can delete users'], 403);
        }

        if ($user->id === $currentUser->id) {
            return response()->json(['message' => 'Unauthorized: You cannot delete yourself'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted']);
    }
}
