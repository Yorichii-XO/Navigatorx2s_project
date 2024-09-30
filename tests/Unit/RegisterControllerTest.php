<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase; // Use the RefreshDatabase trait to run migrations and rollback

    public function test_user_can_register()
    {
        // Arrange: Prepare the registration data using factory
        $user = User::factory()->make(); // Create a user instance without saving it to the database

        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'password123',
            'role_id' => 1, // Optional role_id
        ];

        // Act: Call the register endpoint
        $response = $this->postJson('/api/register', $data);

        // Assert: Check if the user was created and the response is correct
        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'Registered successfully',
                     'user' => [
                         'name' => $user->name,
                         'email' => $user->email,
                         'role_id' => 1,
                         'is_active' => 'inactive', // Assert inactive as default
                     ]
                 ]);

        // Assert that the user is actually in the database
        $this->assertDatabaseHas('users', [
            'name' => $user->name,
            'email' => $user->email,
            'role_id' => 1,
            'is_active' => 'inactive',
        ]);

        // Check if the password is hashed
        $this->assertTrue(Hash::check('password123', User::first()->password));
    }

    public function test_registration_fails_without_required_fields()
    {
        // Act: Call the register endpoint with missing fields
        $response = $this->postJson('/api/register', []);

        // Assert: Check if the validation errors are returned
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_registration_fails_with_invalid_email()
    {
        // Arrange: Prepare the registration data with invalid email
        $data = [
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'password' => 'password123',
        ];

        // Act: Call the register endpoint
        $response = $this->postJson('/api/register', $data);

        // Assert: Check if the validation error for email is returned
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    public function test_registration_fails_with_duplicate_email()
    {
        // Arrange: Create a user to simulate duplicate email scenario using factory
        $existingUser = User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        // Arrange: Prepare the registration data with duplicate email
        $data = [
            'name' => 'John Doe',
            'email' => 'existing@example.com', // Duplicate email
            'password' => 'password123',
        ];

        // Act: Call the register endpoint
        $response = $this->postJson('/api/register', $data);

        // Assert: Check if the validation error for email uniqueness is returned
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }
}
