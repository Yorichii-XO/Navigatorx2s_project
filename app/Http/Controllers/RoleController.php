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

    // Store a newly created role in storage
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
        ]);

        return response()->json($role, 201); // Return the created role with a 201 status
    }

    // Display the specified role
    public function show(Role $role)
    {
        return response()->json($role);
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

        return response()->json($role);
    }

    // Remove the specified role from storage
    public function destroy(Role $role)
    {
        $role->delete();
        return response()->json(null, 204); // Return a 204 status for successful deletion with no content
    }
}
