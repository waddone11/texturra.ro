<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ManufactoringTypeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('manufactoring_types')->insert([
            [
                'name' => 'Manoperă rejanșă',
                'price' => 9.00,
            ],
            [
                'name' => 'Manoperă capse',
                'price' => 30.00,
            ],
            [
                'name' => 'Rejansă galerie',
                'price' => 12.00,
            ],
            [
                'name' => 'Rejansă 10 cm',
                'price' => 11.00,
            ],
            [
                'name' => 'Rejansă tiv lat',
                'price' => 8.00,
            ],
            [
                'name' => 'Fără manoperă',
                'price' => 0.00,
            ],
        ]);
    }
}
