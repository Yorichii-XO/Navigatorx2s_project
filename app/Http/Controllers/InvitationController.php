<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class InvitationController extends Controller
{
    /**
     * Invite a user via email.
     */
    public function invite(Request $request)
    {
        // Validate the email input
        $request->validate([
            'email' => 'required|email',
        ]);

        try {
            // Create a new invitation with the logged-in user as the inviter
            $invitation = Invitation::create([
                'email' => $request->email,
                'invited_by' => Auth::id(),
            ]);

            // Send email notification to the invited user

            return response()->json(['message' => 'Invitation sent successfully!']);
        } catch (\Exception $e) {
            Log::error('Error sending invitation: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to send invitation'], 500);
        }
    }

    /**
     * Fetch all invitations for the authenticated user.
     */
// Method to retrieve members invited by the authenticated user
 public function index()
    {
        try {
            // Get the authenticated user's email
            $userEmail = Auth::user()->email;

            // Fetch invitations where the email matches the authenticated user's email
            $invitations = Invitation::with('inviter')
                ->where('email', $userEmail) // Filter invitations for this user's email
                ->get();

            // Transform the invitations data to include the inviter's name
            $invitations = $invitations->map(function ($invitation) {
                return [
                    'id' => $invitation->id,
                    'email' => $invitation->email,
                    'invited_by' => $invitation->inviter->name ?? 'Unknown', // Ensure inviter relationship works, handle null cases
                    'created_at' => $invitation->created_at,
                ];
            });

            return response()->json($invitations);
        } catch (\Exception $e) {
            // Log the error and return a 500 response
            Log::error('Error fetching invitations: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch invitations'], 500);
        }
    }
    public function acceptInvitation(Request $request, $id)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id', // Assuming you have a roles table
        ]);
    
        try {
            // Find the invitation by ID
            $invitation = Invitation::findOrFail($id);
    
            // Check if the invitation is already accepted
            if ($invitation->accepted) {
                return response()->json(['message' => 'Invitation already accepted.'], 400);
            }
    
            // Get the authenticated user's ID
            $authenticatedUserId = auth()->id(); // Retrieve the ID of the currently authenticated user
    
            // Optionally, get the authenticated user's name if the invitation doesn't have one
            $authenticatedUser = User::findOrFail($authenticatedUserId);
    
            // Create a new member with the authenticated user's ID
            Member::create([
                'user_id' => $authenticatedUserId,      // Use the authenticated user's ID
                'name' => $authenticatedUser->name,     // Use the authenticated user's name
                'email' => $invitation->email,           // Assuming you have the email in the invitation
                'password' => $authenticatedUser->password, // Password from the authenticated user (hashed)
                'role_id' => $validated['role_id'],      // Coming from the request
                'invited_by' => $invitation->invited_by, // The ID of the inviter from the invitation
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
    

    
    
    

}
