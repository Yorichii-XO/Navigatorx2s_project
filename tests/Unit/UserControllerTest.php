<?php

namespace Tests\Unit;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created_with_valid_data()
    {
        $role = Role::factory()->create();

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User',
            'is_active' => true,
        ]);

        $this->assertTrue($user->is_active);
    }

   

 
 
    public function test_user_can_be_updated_with_valid_data()
    {
        $role = Role::factory()->create();
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
            'is_active' => false,
        ]);

        $user->update([
            'name' => 'Updated User',
            'email' => 'updated@example.com',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'updated@example.com',
            'name' => 'Updated User',
            'is_active' => true,
        ]);
    }

    public function test_user_can_be_deleted()
    {
        $role = Role::factory()->create();
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
        ]);

        $user->delete();

        $this->assertDatabaseMissing('users', [
            'email' => 'test@example.com',
        ]);
    }

    public function test_user_status_is_active()
    {
        $role = Role::factory()->create();
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
            'is_active' => true,
        ]);

        $this->assertTrue($user->is_active);
    }

    public function test_user_status_is_inactive()
    {
        $role = Role::factory()->create();
        $user = User::create([
            'name' => 'Inactive User',
            'email' => 'inactive@example.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
            'is_active' => false,
        ]);

        $this->assertFalse($user->is_active);
    }
}
