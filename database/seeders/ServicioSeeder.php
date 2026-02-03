<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Servicio;

class ServicioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $servicios = [
            [
                'nombre' => 'Diagnóstico General',
                'descripcion' => 'Revisión completa del vehículo para identificar problemas',
                'tipo' => 'diagnostico',
                'precio_base' => 50.00,
                'duracion_estimada' => 60,
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Cambio de Aceite',
                'descripcion' => 'Cambio de aceite y filtro',
                'tipo' => 'mantenimiento',
                'precio_base' => 30.00,
                'duracion_estimada' => 30,
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Alineación y Balanceo',
                'descripcion' => 'Alineación de ruedas y balanceo de llantas',
                'tipo' => 'mantenimiento',
                'precio_base' => 40.00,
                'duracion_estimada' => 45,
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Reparación de Frenos',
                'descripcion' => 'Reparación o reemplazo de sistema de frenos',
                'tipo' => 'reparacion',
                'precio_base' => 100.00,
                'duracion_estimada' => 120,
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Cambio de Batería',
                'descripcion' => 'Reemplazo de batería del vehículo',
                'tipo' => 'reparacion',
                'precio_base' => 80.00,
                'duracion_estimada' => 20,
                'estado' => 'activo',
            ],
        ];

        foreach ($servicios as $servicio) {
            Servicio::create($servicio);
        }
    }
}