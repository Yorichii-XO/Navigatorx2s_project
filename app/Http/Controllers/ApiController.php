<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // You can implement the logic to return a list of resources if needed
    }

    /**
     * Handle user login.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // Validate the incoming request data
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
    
        // Attempt to authenticate the user
        if (!Auth::attempt($credentials)) {
            Log::error('Login attempt failed', ['credentials' => $credentials]);
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }
    
        // Get the authenticated user
        $user = Auth::user();
    
        // Set user as active
        $user->is_active = 'active'; // Set status as string "active"
        $user->save();
    
        // Generate an authentication token
        $token = $user->createToken('auth_token')->plainTextToken;
    
        // Return the response including the user's role and active status
        return response()->json([
            'success' => true,
            'token' => $token,
            'token_type' => 'Bearer',
            'status' => 200,
            'role' => $user->role,
            'is_active' => $user->is_active, // Include user active status in the response
        ]);
    }
    

    /**
     * Get a list of users.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsers()
    {
        $users = User::with('role')->get(); // Eager load roles
        return response()->json([
            'success' => true,
            'data' => $users,
            'status' => 200,
        ]);
    }

    /**
     * Store a newly created user in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id', // Assuming you're assigning a role
            'is_active' => 'required|boolean', // Assuming you want to set active status
        ]);

        // Create a new user
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role_id' => $validatedData['role_id'],
            'is_active' => $validatedData['is_active'], // Set active status
        ]);

        return response()->json([
            'success' => true,
            'data' => $user,
            'status' => 201,
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = User::with('role')->find($id); // Eager load the role

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $user,
            'status' => 200,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        // Validate incoming request data
        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|nullable|string|min:8|confirmed',
            'role_id' => 'sometimes|nullable|exists:roles,id',
            'is_active' => 'sometimes|boolean',
        ]);

        $user->update(array_filter($validatedData)); // Update user with validated data

        return response()->json([
            'success' => true,
            'data' => $user,
            'status' => 200,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
            'status' => 200,
        ]);
    }
    public function logout(Request $request)
    {
        // Get the currently authenticated user
        $user = $request->user();
    
        // Update user status to inactive
        if ($user) {
            $user->is_active = 'inactive'; // Set status as string "inactive"
            $user->save();
        }
    
        // Delete all tokens for the user (if using Sanctum for token authentication)
        $user->tokens()->delete();
    
        // Return a response
        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out',
            'is_active' => $user->is_active, // Include the updated active status
        ]);
    }
    
}
