<?php

namespace Database\Factories;

use App\Models\Invitation;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvitationFactory extends Factory
{
    protected $model = Invitation::class;

    public function definition()
    {
        return [
            'email' => $this->faker->unique()->safeEmail(),
            'invited_by' => \App\Models\User::factory(), // Assumes User factory exists
            'accepted' => false, // Default value for accepted invitations
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
