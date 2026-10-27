<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // index.blade.php içindeki kabinler/dolaplar listesini DB'ye aktar.
        $this->call(ModellerFromIndexSeeder::class);

        // index.blade.php içindeki palette renklerini DB'ye aktar.
        $this->call(RenklerFromIndexSeeder::class);

        // public/on-bellek altındaki tüm görselleri model-renk eşleşmelerine aktar.
        $this->call(ModelColorMatchesFromOnBellekSeeder::class);
    }
}
