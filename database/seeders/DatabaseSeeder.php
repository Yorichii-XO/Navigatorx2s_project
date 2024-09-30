<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            ActivitySeeder::class,
            RoleSeeder::class,
            MemberSeeder::class,
            InvitationSeeder::class,
            UrlVisitSeeder::class
        ]);

    }
}
