<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Category;

class TextileCategorySeeder extends Seeder
{
    public function run()
    {
        $structure = [
            'Perdele' => [
                'Perdele voal',
                'Perdele brodate',
                'Perdele uni',
                'Perdele cu imprimeu',
                'Perdele blackout decorative',
            ],
            'Draperii' => [
                'Draperii blackout',
                'Draperii catifea',
                'Draperii uni',
                'Draperii cu model',
            ],
            'Lenjerii de pat' => [
                'Lenjerii bumbac 100%',
                'Lenjerii satinate',
                'Lenjerii creponate',
                'Seturi cu pilota',
                'Lenjerii pentru copii',
            ],
            'Covoare' => [
                'Covoare moderne',
                'Covoare clasice',
                'Covoare copii',
                'Covoare baie',
                'Covoare bucătărie',
            ],
            'Accesorii' => [
                'Inele și cârlige',
                'Ciucuri și prinderi',
                'Rejansa',
            ],
//            'Broderii & Țesături' => [
//                'Broderii personalizate',
//                'Țesături decorative',
//                'Țesături pentru draperii',
//                'Țesături pentru perdele',
//            ],
            'Galerii & Sine' => [
                'Șine perdele',
                'Galerii draperii',
            ],
        ];

        foreach ($structure as $parent => $children) {
            $parentCategory = Category::firstOrCreate(
                ['name' => $parent],
                [
                    'slug' => Str::slug($parent),
                    'status' => 1,
                    'description' => '',
                    'source' => 'texturra',
                    'source_status' => 0,
                    'source_link' => null,
                    'name_seo' => "$parent - Textile pentru casă și decor",
                    'description_seo' => "Explorează colecția de $parent pentru un decor elegant și funcțional.",
                    'image' => 'storage/images/icons/' . Str::slug($parent) . '.png',
                ]
            );

            foreach ($children as $child) {
                Category::firstOrCreate(
                    ['name' => $child, 'parent_id' => $parentCategory->id],
                    [
                        'slug' => Str::slug($child),
                        'status' => 1,
                        'description' => '',
                        'source' => 'texturra',
                        'source_status' => 0,
                        'name_seo' => "$child - $parent",
                        'description_seo' => "$child din categoria $parent oferă stil și calitate pentru casa ta.",
                    ]
                );
            }
        }
    }
}
