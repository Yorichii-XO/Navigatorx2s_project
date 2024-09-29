<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Retrieve the authenticated user's ID
            $userId = Auth::id(); // Get the ID of the authenticated user
    
            // Fetch only the members invited by the authenticated user along with their is_active status
            $members = Member::with('inviter')
                ->where('invited_by', $userId)
                ->join('users', 'members.user_id', '=', 'users.id') // Adjust this line based on your actual table structure
                ->select('members.*', 'users.is_active') // Select members' attributes and is_active from users
                ->get();
    
            return response()->json($members);
        } catch (\Exception $e) {
            Log::error('Error fetching members: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch members'], 500);
        }
    }
    

    // Accept invitation method (unchanged)
    public function acceptInvitation(Request $request, $id)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'required|boolean',
        ]);

        try {
            // Find the invitation by ID
            $invitation = Invitation::findOrFail($id);

            // Check if the invitation is already accepted
            if ($invitation->accepted) {
                return response()->json(['message' => 'Invitation already accepted.'], 400);
            }

            // Fetch the user that corresponds to the email in the invitation
            $user = User::where('email', $invitation->email)->firstOrFail();

            // Create a new member with the data from the User table
            Member::create([
                'name' => $user->name,
                'email' => $user->email,
                'password' => $user->password,
                'role_id' => $validated['role_id'],
                'is_active' => $validated['is_active'],
                'invited_by' => $invitation->invited_by,
            ]);

            // Mark the invitation as accepted
            $invitation->accepted = true;
            $invitation->save();

            return response()->json(['message' => 'Invitation accepted, and member created successfully.']);
        } catch (\Exception $e) {
            Log::error('Error accepting invitation: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to accept invitation'], 500);
        }
    }
 // Method to delete a member
 public function deleteMember($id)
 {
     try {
         $member = Member::findOrFail($id);
         $member->delete();

         return response()->json(['message' => 'Member deleted successfully.']);
     } catch (\Exception $e) {
         Log::error('Error deleting member: ' . $e->getMessage());
         return response()->json(['error' => 'Failed to delete member'], 500);
     }
 }

  // Fetch member data by ID
  public function edit($id)
  {
      $member = Member::find($id);
      
      if (!$member) {
          return response()->json(['error' => 'Member not found'], 404);
      }
      
      return response()->json($member);
  }

  // Update member data
  public function update(Request $request, $id)
  {
      $member = Member::find($id);
      
      if (!$member) {
          return response()->json(['error' => 'Member not found'], 404);
      }

      // Validate incoming data
      $validatedData = $request->validate([
          'name' => 'required|string|max:255',
          'email' => 'required|email|max:255|unique:members,email,' . $id,
      ]);

      // Update member attributes
      $member->name = $validatedData['name'];
      $member->email = $validatedData['email'];
      $member->save();

      return response()->json(['message' => 'Member updated successfully']);
  }}