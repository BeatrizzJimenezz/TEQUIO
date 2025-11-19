<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UsersRolesSeeder extends Seeder
{
    public function run(): void
    {
       // Crear roles si no existen
        $roles = ['Administrador', 'Organizador', 'Participante'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }
        
        // usuario Administrador
        $admin = User::firstOrCreate(
            ['email' => 'admin@tequio.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('1234')
            ]
        );
        $admin->assignRole('Administrador');

        // usuario Organizador
        $organizador = User::firstOrCreate(
            ['email' => 'organizador@tequio.com'],
            [
                'name' => 'Organizador',
                'password' => Hash::make('1234')
            ]
        );
        $organizador->assignRole('Organizador');

        // usuario Participante
        $participante = User::firstOrCreate(
            ['email' => 'participante@tequio.com'],
            [
                'name' => 'Participante',
                'password' => Hash::make('1234')
            ]
        );
        $participante->assignRole('Participante');
    }
}
