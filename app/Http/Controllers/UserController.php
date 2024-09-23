<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
  // Display a listing of users
  public function index()
  {
      $users = User::with('role')->get();
      return response()->json($users);
  }

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

      User::create([
          'name' => $validated['name'],
          'email' => $validated['email'],
          'password' => bcrypt($validated['password']),
          'role_id' => $validated['role_id'],
      ]);

      return redirect()->route('users.index');
  }

  // Display the specified user
  public function show(User $user)
  {
      return response()->json($user->load('role'));
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
      ]);

      $user->update([
          'name' => $validated['name'],
          'email' => $validated['email'],
          'password' => $validated['password'] ? bcrypt($validated['password']) : $user->password,
          'role_id' => $validated['role_id'],
      ]);

      return redirect()->route('users.index');
  }

  // Remove the specified user from storage
  public function destroy(User $user)
  {
      $user->delete();
      return redirect()->route('users.index');
  }
}