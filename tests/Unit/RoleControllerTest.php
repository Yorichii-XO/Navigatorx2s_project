<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Role;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_role()
    {
        $role = Role::factory()->create([
            'name' => 'admin',
        ]);

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'admin',
        ]);
    }

    /** @test */
    public function it_has_a_name()
    {
        $role = Role::factory()->create([
            'name' => 'editor',
        ]);

        $this->assertEquals('editor', $role->name);
    }

    /** @test */
    public function it_requires_a_name()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Role::create([
            'name' => null, // assuming name cannot be null
        ]);
    }

    /** @test */
    public function it_can_update_a_role()
    {
        $role = Role::factory()->create([
            'name' => 'viewer',
        ]);

        $role->name = 'supervisor';
        $role->save();

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'supervisor',
        ]);
    }

    /** @test */
    public function it_can_delete_a_role()
    {
        $role = Role::factory()->create();

        $roleId = $role->id;
        $role->delete();

        $this->assertDatabaseMissing('roles', [
            'id' => $roleId,
        ]);
    }
}
