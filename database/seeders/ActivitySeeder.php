<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activity;

class ActivitySeeder extends Seeder
{
    public function run()
    {
        // Create 10 activities
        Activity::factory()->count(10)->create();
    }
}
