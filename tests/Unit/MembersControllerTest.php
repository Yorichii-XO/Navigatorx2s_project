<?php

namespace Tests\Unit;

use App\Models\Invitation;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberControllerTest extends TestCase
{
    use RefreshDatabase;


    /** @test */
    public function it_can_accept_invitation()
    {
        $user = User::factory()->create();
        // Create an invitation associated with the user
        $invitation = Invitation::factory()->create(['invited_by' => $user->id]);

        $data = ['role_id' => 1];

        $response = $this->actingAs($user)->postJson('/api/invitations/' . $invitation->id . '/accept', $data);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Invitation accepted, and member created successfully.']);
        

    }

   

    /** @test */
    public function it_can_delete_member()
    {
        $user = User::factory()->create();
        // Create a member associated with the user
        $member = Member::factory()->create(['invited_by' => $user->id]);

        $response = $this->actingAs($user)->deleteJson('/api/members/' . $member->id);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Member deleted successfully.']);
        
        // Check that the member no longer exists in the database
        $this->assertDatabaseMissing('members', ['id' => $member->id]);
    }

   

    /** @test */
    public function it_can_update_member()
    {
        $user = User::factory()->create();
        // Create a member associated with the user
        $member = Member::factory()->create(['invited_by' => $user->id]);

        $data = ['name' => 'Updated Name', 'email' => 'updated@example.com'];

        $response = $this->actingAs($user)->putJson('/api/members/' . $member->id, $data);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Member updated successfully']);
        
        // Check that the member's name was updated in the database
        $this->assertDatabaseHas('members', ['id' => $member->id, 'name' => 'Updated Name']);
    }

   

   
}
