<?php

namespace Database\Seeders;

use App\Models\Renk;
use Illuminate\Database\Seeder;

class RenklerFromIndexSeeder extends Seeder
{
    public function run(): void
    {
        $palette = [
            ['name' => 'CN-X01 I ULTRA WHİTE', 'code' => '#f3f3ec'],
            ['name' => 'CN-S01 I BEIGE', 'code' => '#efebcf'],
            ['name' => 'CN-N01 I MAGNOLIA', 'code' => '#ccc8b8'],
            ['name' => 'CN-G01 I OFFICE GRAY', 'code' => '#d5d3d1'],
            ['name' => 'CN-G03 I DARK GREY', 'code' => '#878a87'],
            ['name' => 'CN-G02 I ANTHRACITE', 'code' => '#434544'],
            ['name' => 'CN-X03 I MYSTIK NEW', 'code' => '#ac969a'],
            ['name' => 'CN-A01 I AZUL BLUE', 'code' => '#405080'],
            ['name' => 'CN-A02 I ORANGE', 'code' => '#d27648'],
            ['name' => 'CN-X01 CHROME YELLOW', 'code' => '#ebc478'],
        ];

        foreach ($palette as $item) {
            // Aynı renk (aynı hex/code) 2 kere eklenmesin diye code üzerinden güncelliyoruz.
            Renk::updateOrCreate(
                ['code' => $item['code']],
                [
                    'name' => $item['name'],
                    'type' => 'standart',
                    'image' => null,
                    'status' => 'active',
                ],
            );
        }
    }
}

