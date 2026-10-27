<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if admin user already exists
        $adminExists = User::where('email', 'admin@noter.com')->exists();

        if (!$adminExists) {
            User::create([
                'username' => 'admin',
                'email' => 'admin@noter.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]);

            $this->command->info('Admin kullanıcısı oluşturuldu!');
            $this->command->info('E-posta: admin@noter.com');
            $this->command->info('Şifre: admin123');
        } else {
            $this->command->info('Admin kullanıcısı zaten mevcut.');
        }
    }
}
