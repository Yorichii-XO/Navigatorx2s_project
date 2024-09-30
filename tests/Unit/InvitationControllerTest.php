<?php

namespace Tests\Unit;

use App\Http\Controllers\InvitationController;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $invitationController;

    protected function setUp(): void
    {
        parent::setUp();
        $this->invitationController = new InvitationController();
    }

    public function test_invite()
    {
        // Create a mock user and authenticate
        $user = User::factory()->create();
        Auth::shouldReceive('id')->andReturn($user->id);

        // Create a request with valid data
        $request = Request::create('/api/invite', 'POST', ['email' => 'test@example.com']);

        // Call the invite method
        $response = $this->invitationController->invite($request);

        // Assert that the response is successful
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Invitation sent successfully!']),
            $response->getContent()
        );

        // Assert the invitation is created
        $this->assertDatabaseHas('invitations', [
            'email' => 'test@example.com',
            'invited_by' => $user->id,
        ]);
    }

    

    public function test_index()
    {
        // Create a mock user and authenticate
        $user = User::factory()->create();
        Auth::shouldReceive('user')->andReturn($user);

        // Create invitations for the user
        Invitation::factory()->create(['email' => $user->email, 'invited_by' => $user->id]);

        // Call the index method
        $response = $this->invitationController->index();

        // Assert the response structure
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_accept_invitation()
    {
        // Create a mock user and authenticate
        $user = User::factory()->create(['password' => bcrypt('password')]);
        Auth::shouldReceive('id')->andReturn($user->id);

        // Create an invitation
        $invitation = Invitation::factory()->create(['email' => 'test@example.com', 'invited_by' => $user->id]);

        // Create a request with valid role_id
        $request = Request::create("/api/invitations/{$invitation->id}/accept", 'POST', ['role_id' => 1]);

        // Call the acceptInvitation method
        $response = $this->invitationController->acceptInvitation($request, $invitation->id);

        // Assert that the response is successful
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Invitation accepted, and member created successfully.']),
            $response->getContent()
        );

        // Assert the invitation is marked as accepted
        $this->assertDatabaseHas('invitations', [
            'id' => $invitation->id,
            'accepted' => true,
        ]);
    }

    public function test_accept_invitation_already_accepted()
    {
        // Create a mock user and authenticate
        $user = User::factory()->create(['password' => bcrypt('password')]);
        Auth::shouldReceive('id')->andReturn($user->id);

        // Create an invitation and mark it as accepted
        $invitation = Invitation::factory()->create(['email' => 'test@example.com', 'invited_by' => $user->id, 'accepted' => true]);

        // Create a request with valid role_id
        $request = Request::create("/api/invitations/{$invitation->id}/accept", 'POST', ['role_id' => 1]);

        // Call the acceptInvitation method
        $response = $this->invitationController->acceptInvitation($request, $invitation->id);

        // Assert that the response indicates the invitation has already been accepted
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Invitation already accepted.']),
            $response->getContent()
        );
    }
}
