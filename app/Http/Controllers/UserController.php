<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function searchByEmail(Request $request)
    {
        $email = $request->query('email');
        
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        return response()->json(['data' => $user]);
    }

    public function indexAdmins()
    {
        $admins = User::where('is_admin', true)->get();
        return response()->json(['data' => $admins]);
    }

    public function toggleAdmin(User $user)
    {
        $user->update([
            'is_admin' => !$user->is_admin
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User role updated successfully.',
            'is_admin' => $user->is_admin
        ]);
    }
}