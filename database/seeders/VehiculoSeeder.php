<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehiculo;
use App\Models\User;

class VehiculoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientes = User::where('tipo', 'cliente')->get();

        foreach ($clientes as $cliente) {
            Vehiculo::create([
                'cliente_id' => $cliente->id,
                'placa' => 'ABC' . str_pad($cliente->id, 3, '0', STR_PAD_LEFT),
                'marca' => ['Toyota', 'Honda', 'Ford', 'Chevrolet', 'Nissan'][array_rand(['Toyota', 'Honda', 'Ford', 'Chevrolet', 'Nissan'])],
                'modelo' => ['Corolla', 'Civic', 'Focus', 'Cruze', 'Sentra'][array_rand(['Corolla', 'Civic', 'Focus', 'Cruze', 'Sentra'])],
                'anio' => rand(2010, 2023),
                'color' => ['Rojo', 'Azul', 'Negro', 'Blanco', 'Gris'][array_rand(['Rojo', 'Azul', 'Negro', 'Blanco', 'Gris'])],
                'kilometraje' => rand(10000, 150000),
                'observaciones' => 'Vehículo en buen estado',
                'estado' => 'activo',
            ]);
        }

        // Agregar un vehículo extra para el primer cliente
        Vehiculo::create([
            'cliente_id' => $clientes->first()->id,
            'placa' => 'XYZ123',
            'marca' => 'BMW',
            'modelo' => 'X3',
            'anio' => 2020,
            'color' => 'Negro',
            'kilometraje' => 50000,
            'observaciones' => 'SUV de lujo',
            'estado' => 'activo',
        ]);
    }
}