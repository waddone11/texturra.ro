<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('products')->truncate();
        Schema::enableForeignKeyConstraints();

        DB::table('products')->insert([
            [
                'id' => 1,
                'name' => 'Draperie Velvet 04 – Violet Regal Blackout din Catifea de Lux',
                'slug' => 'draperie-velvet-04-violet-regal-blackout-din-catifea-de-lux-id-1',
                'description' => '<p>Transformă-ți spațiul într-un sanctuar al rafinamentului cu această draperie <strong>velvet blackout</strong> într-o nuanță intensă de violet regal. Confecționată din catifea de înaltă calitate, această draperie adaugă profunzime și lux oricărui decor interior, oferind totodată un control excelent al luminii și intimitate deplină.</p><p>✔ <strong>Material blackout</strong> – Blochează eficient lumina solară și protejează mobilierul de razele UV</p><p> ✔ <strong>Textură velvet premium</strong> – Catifea moale, cu aspect bogat și elegant</p><p> ✔ <strong>Finisaj profesional</strong> – Cădere perfectă și prindere fermă, ideală pentru ferestre mari</p><p> ✔ <strong>Ideală pentru livinguri sofisticate, dormitoare rafinate sau spații de relaxare</strong></p><p> ✔ <strong>Efect acustic atenuant</strong> – Reduce zgomotul exterior pentru un ambient liniștit</p><p>Această draperie nu este doar un accesoriu decorativ, ci un element statement ce îmbină estetica cu funcționalitatea. O alegere ideală pentru cei care își doresc o atmosferă elegantă, intimă și complet personalizată.</p>',
                'price' => 45.00,
                'type' => 'custom',
                'height' => 2.80,
                'category_id' => 8,
                'images' => json_encode(["/storage/images/uploads/products/draperie-velvet-blackout-04-9259fe.jpg"]),
                'barcode' => '4444',
                'ownership' => 1,
                'general_stock' => 1000,
                'stock' => 0,
                'status' => 1,
                'is_synced' => 0,
                'product_code' => 'TEX-682F39D449158',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Add other 21 products here (manually or via script)
        ]);
    }
}
