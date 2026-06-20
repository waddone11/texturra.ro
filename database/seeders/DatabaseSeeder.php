<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed textile-related data
        $this->call([
            ColorGroupsSeeder::class,
            ColorsSeeder::class,
            TextileCategorySeeder::class,
        ]);
    }
}
