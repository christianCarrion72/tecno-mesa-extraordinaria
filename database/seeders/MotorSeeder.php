<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Motor;

class MotorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $motores = [
            [
                'numero_serie' => 'TOY-2024-001',
                'anio' => 2024,
                'descripcion' => 'Motor 1.8L 4 cilindros',
                'marca_id' => 1,
                'modelo_id' => 1,
                'foto' => null,
            ],
            [
                'numero_serie' => 'HON-2023-002',
                'anio' => 2023,
                'descripcion' => 'Motor 2.0L turbo',
                'marca_id' => 2,
                'modelo_id' => 2,
                'foto' => null,
            ],
            [
                'numero_serie' => 'FOR-2024-003',
                'anio' => 2024,
                'descripcion' => 'Motor 3.5L V6',
                'marca_id' => 3,
                'modelo_id' => 3,
                'foto' => null,
            ],
            [
                'numero_serie' => 'CHE-2023-004',
                'anio' => 2023,
                'descripcion' => 'Motor 5.3L V8',
                'marca_id' => 4,
                'modelo_id' => 4,
                'foto' => null,
            ],
            [
                'numero_serie' => 'NIS-2024-005',
                'anio' => 2024,
                'descripcion' => 'Motor 2.5L 4 cilindros',
                'marca_id' => 5,
                'modelo_id' => 5,
                'foto' => null,
            ],
        ];

        foreach ($motores as $motor) {
            Motor::create($motor);
        }
    }
}
