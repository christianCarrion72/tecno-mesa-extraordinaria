<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Propietario
        User::create([
            'nombre' => 'Juan Pérez',
            'email' => 'propietario@taller.com',
            'password_hash' => Hash::make('password'),
            'telefono' => '123456789',
            'direccion' => 'Calle Principal 123',
            'tipo' => 'propietario',
            'estado' => 'activo',
        ]);

        // Secretaria
        User::create([
            'nombre' => 'María García',
            'email' => 'secretaria@taller.com',
            'password_hash' => Hash::make('password'),
            'telefono' => '987654321',
            'direccion' => 'Avenida Central 456',
            'tipo' => 'secretaria',
            'estado' => 'activo',
        ]);

        // Mecánicos
        User::create([
            'nombre' => 'Carlos López',
            'email' => 'mecanico1@taller.com',
            'password_hash' => Hash::make('password'),
            'telefono' => '555666777',
            'direccion' => 'Calle de los Mecánicos 789',
            'tipo' => 'mecanico',
            'estado' => 'activo',
        ]);

        User::create([
            'nombre' => 'Ana Rodríguez',
            'email' => 'mecanico2@taller.com',
            'password_hash' => Hash::make('password'),
            'telefono' => '444333222',
            'direccion' => 'Avenida de las Herramientas 101',
            'tipo' => 'mecanico',
            'estado' => 'activo',
        ]);

        // Clientes
        User::create([
            'nombre' => 'Pedro Sánchez',
            'email' => 'cliente1@taller.com',
            'password_hash' => Hash::make('password'),
            'telefono' => '111222333',
            'direccion' => 'Plaza Mayor 202',
            'tipo' => 'cliente',
            'estado' => 'activo',
        ]);

        User::create([
            'nombre' => 'Laura Martínez',
            'email' => 'cliente2@taller.com',
            'password_hash' => Hash::make('password'),
            'telefono' => '999888777',
            'direccion' => 'Calle del Comercio 303',
            'tipo' => 'cliente',
            'estado' => 'activo',
        ]);

        User::create([
            'nombre' => 'Roberto Díaz',
            'email' => 'cliente3@taller.com',
            'password_hash' => Hash::make('password'),
            'telefono' => '666777888',
            'direccion' => 'Boulevard Industrial 404',
            'tipo' => 'cliente',
            'estado' => 'activo',
        ]);
    }
}