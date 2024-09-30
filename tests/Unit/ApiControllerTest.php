<?php

namespace Tests\Unit;

use App\Models\Role;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ApiControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_login_a_user()
    {
        // Create a role first
        $role = Role::factory()->create(['name' => 'Admin']); // Use factory to create the role
    
        // Create a user with a valid role
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role_id' => $role->id,
            'password' => Hash::make('password'),
            'is_active' => 'inactive', // initial status
        ]);
    
        // Attempt to log in
        $response = $this->postJson('/api/login', [
            'email' => 'john@example.com',
            'password' => 'password',
        ]);
    
        // Check the response
        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'token' => $response->json('token'), // Check for the token
                     'role' => [
                         'id' => $role->id,
                         'name' => $role->name,
                     ],
                     'is_active' => 'active',
                 ]);
    
        // Ensure the user's status is updated to active
        $this->assertEquals('active', $user->fresh()->is_active);
    }
    

    /** @test */
    public function it_fails_login_with_invalid_credentials()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
                 ->assertJson(['success' => false, 'message' => 'Invalid credentials']);
    }


}
