<?php

namespace Database\Seeders;

use App\Models\UrunModel;
use Illuminate\Database\Seeder;

class ModellerFromIndexSeeder extends Seeder
{
    public function run(): void
    {
        $kabinler = [
            ['name' => 'PROXLINE KLASİK', 'image' => 'assets/PROXLINE-KLASIK-kabin.jpg', 'type' => 'kabin'],
            ['name' => 'MULTISILVER', 'image' => 'assets/MULTISILVER-kabin.jpg', 'type' => 'kabin'],
            ['name' => 'PROXLINE BİNİ', 'image' => 'assets/PROXLINE-BINI-kabin.jpg', 'type' => 'kabin'],
            ['name' => 'PROXLINE', 'image' => 'assets/PROXLINE-kabin.jpg', 'type' => 'kabin'],
            ['name' => 'ORION LINE', 'image' => 'assets/ORION-LINE-kabin.jpg', 'type' => 'kabin'],
            ['name' => 'STEELLINE KLASİK', 'image' => 'assets/STEELLINE-KLASIK-kabin.jpg', 'type' => 'kabin'],
            ['name' => 'STEELLINE BİNİ', 'image' => 'assets/STEEELLINEBINI-kabin.jpg', 'type' => 'kabin'],
            ['name' => 'STEELLINE LUX', 'image' => 'assets/STEELLINE-LUX-kabin.jpg', 'type' => 'kabin'],
            ['name' => 'STEELLINE DELUX', 'image' => 'assets/STEELLINE-DELUX-kabin.jpg', 'type' => 'kabin'],
            ['name' => 'EXCLUSIVE TYPE 1', 'image' => 'assets/EXCLUSIVE-TYPE-1-kabin.jpg', 'type' => 'kabin'],
        ];

        $dolaplar = [
            ['name' => 'Z KAPAKLI', 'image' => 'assets/Z-KAPAKLI-dolap.jpg', 'type' => 'dolap'],
            ['name' => 'TEK KAPAKLI', 'image' => 'assets/TEK-KAPAKLI-dolap.jpg', 'type' => 'dolap'],
            ['name' => '2 KAPAKLI', 'image' => 'assets/2-KAPAKLI-dolap.jpg', 'type' => 'dolap'],
            ['name' => '3 KAPAKLI', 'image' => 'assets/3-KAPAKLI-dolap.jpg', 'type' => 'dolap'],
            ['name' => '4 KAPAKLI', 'image' => 'assets/4-KAPAKLI-dolap.jpg', 'type' => 'dolap'],
            ['name' => '5 KAPAKLI', 'image' => 'assets/5-KAPAKLI-dolap.jpg', 'type' => 'dolap'],
        ];

        $items = array_merge($kabinler, $dolaplar);

        foreach ($items as $item) {
            UrunModel::updateOrCreate(
                ['name' => $item['name']],
                [
                    'image' => $item['image'],
                    'type' => $item['type'],
                    'color_variation_count' => 1,
                    'status' => 'active',
                ],
            );
        }
    }
}

