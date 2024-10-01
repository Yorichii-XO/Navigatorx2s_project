<?php 

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
   // In UserController.php

   public function index(Request $request)
   {
       // Fetch the currently authenticated user
       $users=User::All();
           return response()->json($users);
       }

       // If the user is not a super-admin, return an unauthorized response
   

    // Show the form for creating a new user
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

   // Store a newly created user in storage
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
        'role_id' => 'required|exists:roles,id',
    ]);

    // Create the user using the validated data
    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => bcrypt($validated['password']),
        'role_id' => $validated['role_id'],
        'is_active' => false, // Default to inactive when created
    ]);

    // Return the newly created user as a JSON response
    return response()->json($user, 201); // 201 Created status code
}


    public function show($id)
    {
        // Find the user by ID
        $user = User::find($id);
        
        // Check if the user exists
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        
        // Return the user data as a JSON response
        return response()->json($user);
    }

    // Show the form for editing the specified user
    public function edit(User $user)
    {
        // Fetch all roles
        $roles = Role::all();

        // Return JSON response with user and roles data
        return response()->json([
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    // Update the specified user in storage
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'required|boolean', // Validate status update
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'] ? bcrypt($validated['password']) : $user->password,
            'role_id' => $validated['role_id'],
            'is_active' => $validated['is_active'], // Update active status
        ]);

  // Return JSON response with user and roles data
  return response()->json([
    'user' => $user,
   
]);
    }

    // Remove the specified user from storage
    public function destroy(User $user)
    {
        $user->delete();
 
        return response()->json($user);
    }

    // Function to determine if a user is active (Optional)
    private function getUserStatus(User $user)
    {
        // Assuming 'is_active' column exists in the 'users' table
        return $user->is_active ? 'Active' : 'Inactive';
    }
    public function showProfile(Request $request)
    {
        $user = Auth::user(); // Retrieve the authenticated user

        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }
    public function updateprofile(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(), // Ensure the email is unique except for the current user
        ]);

        // Retrieve the authenticated user
        $user = Auth::user();

        // Update user information
        $user->name = $request->input('name');
        $user->email = $request->input('email');

        // Save the changes to the database
        if ($user->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully!',
                'data' => $user,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Error updating profile. Please try again.',
        ], 500);
    }
}
