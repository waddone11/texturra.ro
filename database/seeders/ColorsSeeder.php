<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ColorsSeeder extends Seeder
{
    public function run(): void
    {
        $groups = DB::table('color_groups')->pluck('id', 'name');

        $data = [
            'Roșu & Burgundia' => [
                ['Roșu', '#FF0000'],
                ['Roșu aprins', '#DC143C'],
                ['Burgundia', '#800020'],
                ['Cărămiziu', '#B22222'],
                ['Vișiniu', '#8B0000'],
                ['Roșu închis', '#A52A2A'],
                ['Corai', '#FF7F50'],
                ['Roșu intens', '#C40018'],             // NEW
                ['Roșu bordeaux', '#800000'],           // NEW
                ['Roșu Bordeau', '#800000'],            // NEW (alt spelling)
            ],
            'Roz, Piersică' => [
                ['Roz pal', '#FFC0CB'],
                ['Somon', '#FA8072'],
                ['Corai deschis', '#F88379'],
                ['Roz pastel', '#FFD1DC'],
                ['Piersică', '#FFE5B4'],
            ],
            'Portocaliu' => [
                ['Portocaliu', '#FFA500'],
                ['Mandarina', '#FF7518'],
                ['Portocaliu închis', '#FF8C00'],
                ['Tărtar', '#E97451'],
            ],
            'Galben & Auriu' => [
                ['Galben lămâie', '#FFF700'],
                ['Galben muștar', '#FFDB58'],
                ['Auriu', '#FFD700'],
                ['Galben pastel', '#FDFD96'],
                ['Galben Muștar', '#FFDB58'],           // NEW (exact match, added again to ensure match)
                ['Galben auriu', '#FFD700'],            // NEW
            ],
            'Crem & Bej' => [
                ['Bej', '#F5F5DC'],
                ['Crem', '#FFFDD0'],
                ['Nisipiu', '#D2B48C'],
                ['Ivory', '#FFFFF0'],
                ['Champagne', '#F7E7CE'],
            ],
            'Maro' => [
                ['Maro deschis', '#A0522D'],
                ['Ciocolatiu', '#7B3F00'],
                ['Cappuccino', '#A67B5B'],
                ['Maro închis', '#654321'],
            ],
            'Verde' => [
                ['Verde mentă', '#98FF98'],
                ['Verde deschis', '#90EE90'],
                ['Verde închis', '#006400'],
                ['Verde măsliniu', '#808000'],
                ['Verde lime deschis', '#32CD32'],
                ['Verde măsliniu deschis', '#6B8E23'],
                ['Verde neon', '#39FF14'],
                ['Verde pădure deschis', '#228B22'],
                ['Verde smarald deschis', '#50C878'],
                ['Verde smarald', '#009874'],
                ['Verde iarbă', '#7CFC00'],
                ['Verde măsliniu deschis', '#8FBC8F'],
                ['Verde lime închis', '#32CD32'],
                ['Verde neon deschis', '#00FF00'],
                ['Verde neon închis', '#008000'],
                ['Verde olive', '#808000'],
                ['Verde fistic', '#93C572'],
                ['Verde pădure', '#228B22'],
                ['Verde pastel', '#77DD77'],
                ['Verde lime', '#A4C639'],
                ['Verde smarald închis', '#006400'],    // NEW
                ['Verde jad', '#00875A'],               // NEW
                ['Verde pastel', '#9BC4A7'],            // NEW override
                ['Verde măsliniu închis', '#556B2F'],   // NEW
                ['Verde petrol', '#196F3D'],            // NEW
            ],
            'Albastru & Ou de rață' => [
                ['Albastru', '#0000FF'],                // NEW
                ['Albastru marin', '#000080'],
                ['Albastru pastel', '#AEC6CF'],
                ['Albastru deschis', '#ADD8E6'],
                ['Albastru regal', '#4169E1'],
                ['Bleu', '#B0E0E6'],
                ['Bleu ciel', '#87CEEB'],
                ['Turcoaz', '#40E0D0'],
                ['Turcoaz închis', '#008B8B'],           // NEW
                ['Albastru ou de rață', '#5F9EA0'],
                ['Albastru petrol', '#0F4C5C'],
                ['Albastru petrol închis', '#003E51'],
                ['Albastru-Verzui Pastel', '#7FBFBF'],   // NEW
            ],
            'Mov, Lila & Violet' => [
                ['Mov', '#800080'],
                ['Lila', '#C8A2C8'],
                ['Lavandă', '#E6E6FA'],
                ['Violet', '#8F00FF'],
                ['Prună', '#8E4585'],
                ['Indigo', '#4B0082'],
                ['Violet regal', '#6A0DAD'],
                ['Prună intens', '#580F41'],
                ['Violet prună intens', '#5D3A66'],       // NEW
                ['Violet Imperial', '#4B0082'],           // NEW
                ['Royal Purple', '#7851A9'],              // NEW
            ],
            'Negru, Gri & Argintiu' => [
                ['Negru', '#000000'],
                ['Negru Intens', '#0A0A0A'],              // NEW
                ['Gri deschis', '#D3D3D3'],
                ['Gri închis', '#A9A9A9'],
                ['Gri metalic', '#B0B0B0'],
                ['Argintiu', '#C0C0C0'],
            ],
            'Multicolor' => [
                ['Floral', '#FFC0CB'],
                ['Carouri', '#E0C097'],
                ['Dungi', '#E6E6FA'],
                ['Abstract', '#FAFAD2'],
                ['Etno', '#D8BFD8'],
                ['Paisley', '#FFE4E1'],
            ],
        ];

        foreach ($data as $groupName => $colors) {
            $groupId = $groups[$groupName] ?? null;
            if (!$groupId) continue;

            foreach ($colors as [$name, $css]) {
                DB::table('colors')->updateOrInsert([
                    'color_group_id' => $groupId,
                    'name' => $name,
                ], [
                    'cod_css' => $css,
                ]);
            }
        }
    }
}
