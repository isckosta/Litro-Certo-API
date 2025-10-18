<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        $admin = User::create([
            'name' => 'Admin LitroCerto',
            'email' => 'admin@litrocerto.com.br',
            'password' => Hash::make('admin123'),
            'phone' => '11999999999',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        // Moderator user
        $moderator = User::create([
            'name' => 'Moderador LitroCerto',
            'email' => 'moderador@litrocerto.com.br',
            'password' => Hash::make('moderador123'),
            'phone' => '11988888888',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $moderator->assignRole('moderator');

        // Regular user
        $user = User::create([
            'name' => 'Usuário Teste',
            'email' => 'usuario@teste.com.br',
            'password' => Hash::make('usuario123'),
            'phone' => '11977777777',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $user->assignRole('user');
    }
}
