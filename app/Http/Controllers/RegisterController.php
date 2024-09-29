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
        // Validate incoming request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role_id' => 'sometimes|exists:roles,id', // Make role_id optional
        ]);
 
        // Determine role_id, defaulting to 1 if not provided
        $role_id = $request->role_id ?? 1; // Use role_id from request or default to 1
 
        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $role_id, // Use the determined role_id
            'is_active' => 'inactive', // Set inactive by default or modify as per your logic
        ]);
 
        // Return a success response
        return response()->json([
            'message' => 'Registered successfully',
            'user' => $user // Optionally return user data
        ], 201); // HTTP status 201 for Created
    }
    
    public function logout(Request $request)
{
    // Retrieve the authenticated user
    $user = $request->user();

    // Assuming a relation exists to get the member
    $member = $user->member; // Replace 'member' with the correct relationship name if different

    if ($member) {
        $member->is_active = 0; // Set is_active to 0 (inactive)
        $member->save(); // Save the changes
    }

    // Delete the user's tokens
    $user->tokens()->delete();

    return response()->json([
        'message' => 'Successfully logged out'
    ]);
}

}
