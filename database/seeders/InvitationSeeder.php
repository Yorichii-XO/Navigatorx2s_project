<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Invitation;
use App\Models\User;

class InvitationSeeder extends Seeder
{
    public function run()
    {
        // Create 10 invitations for testing
        Invitation::factory()->count(10)->create([
            'invited_by' => User::factory(), // Create a user for each invitation
        ]);
    }
}
