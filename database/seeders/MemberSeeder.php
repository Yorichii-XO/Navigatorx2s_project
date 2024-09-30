<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Member;
use App\Models\User; // Assuming you have a User model

class MemberSeeder extends Seeder
{
    public function run()
    {
        // Create 10 members with a random user as the inviter
        User::all()->each(function ($user) {
            Member::factory()->count(5)->create([
                'invited_by' => $user->id, // Assuming the user is inviting members
            ]);
        });
    }
}
