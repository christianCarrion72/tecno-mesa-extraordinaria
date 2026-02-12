<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cliente;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientes = [
            ['nombre' => 'Juan Pérez', 'telefono' => '72345678', 'foto' => null],
            ['nombre' => 'María González', 'telefono' => '73456789', 'foto' => null],
            ['nombre' => 'Carlos Rodríguez', 'telefono' => '74567890', 'foto' => null],
            ['nombre' => 'Ana Martínez', 'telefono' => '75678901', 'foto' => null],
            ['nombre' => 'Luis Fernández', 'telefono' => '76789012', 'foto' => null],
            ['nombre' => 'Sofía Ramírez', 'telefono' => '77890123', 'foto' => null],
            ['nombre' => 'Diego Morales', 'telefono' => '78901234', 'foto' => null],
            ['nombre' => 'Lucía Herrera', 'telefono' => '79012345', 'foto' => null],
            ['nombre' => 'Mateo Castro', 'telefono' => '70123456', 'foto' => null],
            ['nombre' => 'Valentina Flores', 'telefono' => '71234567', 'foto' => null],
            ['nombre' => 'Andrés López', 'telefono' => '72222222', 'foto' => null],
            ['nombre' => 'Mariana Silva', 'telefono' => '73333333', 'foto' => null],
            ['nombre' => 'Ricardo Peña', 'telefono' => '74444444', 'foto' => null],
            ['nombre' => 'Isabel Cruz', 'telefono' => '75555555', 'foto' => null],
            ['nombre' => 'Fernando Díaz', 'telefono' => '76666666', 'foto' => null],
        ];

        foreach ($clientes as $cliente) {
            Cliente::create($cliente);
        }
    }
}
