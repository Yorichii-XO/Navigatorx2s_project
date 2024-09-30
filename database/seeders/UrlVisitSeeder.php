<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UrlVisit;

class UrlVisitSeeder extends Seeder
{
    public function run()
    {
        UrlVisit::factory()->count(50)->create(); // Create 50 UrlVisit records
    }
}
