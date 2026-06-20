<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ColorGroupsSeeder extends Seeder {
    public function run(): void {
        DB::table('color_groups')->insert([
            ['name' => 'Roșu & Burgundia', 'image_path' => 'storage/images/colors/rosu_burgundia.avif'],
            ['name' => 'Roz, Piersică', 'image_path' => 'storage/images/colors/ror_piersica.avif'],
            ['name' => 'Portocaliu', 'image_path' => 'storage/images/colors/portocaliu.avif'],
            ['name' => 'Galben & Auriu', 'image_path' => 'storage/images/colors/galben_auriu.avif'],
            ['name' => 'Crem & Bej', 'image_path' => 'storage/images/colors/crem_bej.avif'],
            ['name' => 'Maro', 'image_path' => 'storage/images/colors/maro.avif'],
            ['name' => 'Verde', 'image_path' => 'storage/images/colors/verde.avif'],
            ['name' => 'Albastru & Ou de rață', 'image_path' => 'storage/images/colors/albastru_ou_de_rata.avif'],
            ['name' => 'Mov, Lila & Violet', 'image_path' => 'storage/images/colors/violet_mov_lila.avif'],
            ['name' => 'Negru, Gri & Argintiu', 'image_path' => 'storage/images/colors/negru_gri_argintiu.avif'],
            ['name' => 'Multicolor', 'image_path' => 'storage/images/colors/multicolor.avif'],
        ]);
    }
}
