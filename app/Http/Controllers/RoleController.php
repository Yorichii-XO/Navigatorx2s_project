<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
   // Display a listing of roles
   public function index()
   {
       $roles = Role::all();
       return response()->json($roles);
   }

   // Show the form for creating a new role
   public function create()
   {
       return view('roles.create');
   }

   // Store a newly created role in storage
   public function store(Request $request)
   {
       $validated = $request->validate([
           'name' => 'required|string|max:255|unique:roles,name',
       ]);

       Role::create([
           'name' => $validated['name'],
       ]);

       return redirect()->route('roles.index');
   }

   // Display the specified role
   public function show(Role $role)
   {
       return response()->json($role);
   }

   // Show the form for editing the specified role
   public function edit(Role $role)
   {
       return view('roles.edit', compact('role'));
   }

   // Update the specified role in storage
   public function update(Request $request, Role $role)
   {
       $validated = $request->validate([
           'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
       ]);

       $role->update([
           'name' => $validated['name'],
       ]);

       return redirect()->route('roles.index');
   }

   // Remove the specified role from storage
   public function destroy(Role $role)
   {
       $role->delete();
       return redirect()->route('roles.index');
   }
}