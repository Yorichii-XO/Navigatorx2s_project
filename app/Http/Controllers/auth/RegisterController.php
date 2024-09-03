<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
class RegisterController extends Controller
{
    public function register(Request $request)
    {
        // Define valid roles
        $validRoles = ['user', 'admin', 'editor']; // Add roles as needed
    
        // Validate incoming request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'nullable|string', // Allow any string value
        ]);
    
        // Check if the provided role is valid, otherwise default to 'user'
        $role = in_array($request->role, $validRoles) ? $request->role : 'user';
    
        // Create user with the provided or default role
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
        ]);
    
        // Return a success response
        return response()->json([
            'message' => "Registered successfully",
            'user' => $user // Optionally return user data
        ], 201); // Return HTTP status code 201 (Created)
    }
    
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}
