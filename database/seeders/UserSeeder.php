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
        $admin = User::firstOrCreate(
            ['email' => 'admin@litrocerto.com.br'],
            [
                'name' => 'Admin LitroCerto',
                'password' => Hash::make('admin123'),
                'phone' => '11999999999',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        if (! $admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // Moderator user
        $moderator = User::firstOrCreate(
            ['email' => 'moderador@litrocerto.com.br'],
            [
                'name' => 'Moderador LitroCerto',
                'password' => Hash::make('moderador123'),
                'phone' => '11988888888',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        if (! $moderator->hasRole('moderator')) {
            $moderator->assignRole('moderator');
        }

        // Regular user
        $user = User::firstOrCreate(
            ['email' => 'usuario@teste.com.br'],
            [
                'name' => 'Usuário Teste',
                'password' => Hash::make('usuario123'),
                'phone' => '11977777777',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        if (! $user->hasRole('user')) {
            $user->assignRole('user');
        }
    }
}
