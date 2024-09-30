<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'member_id' => null, // Set if needed
            'browser' => $this->faker->randomElement(['Firefox', 'Chrome', 'Edge']),
            'start_time' => now(),
            'end_time' => null,
            'duration' => null,
            
        ];
    }
}
