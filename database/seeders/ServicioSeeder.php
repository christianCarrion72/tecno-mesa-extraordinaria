<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Servicio;

class ServicioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Servicios enfocados en reparación y rectificación de partes del motor
        $servicios = [
            ['nombre' => 'Cepillado de Bloque', 'descripcion' => 'Cepillado y rectificado del bloque de motor', 'costo' => 1200.00],
            ['nombre' => 'Encamisado de Cilindros', 'descripcion' => 'Encamisado / instalación de camisas de cilindros', 'costo' => 1800.00],
            ['nombre' => 'Cambio de Anillas de Pistón', 'descripcion' => 'Sustitución de anillas/segmentos de pistón', 'costo' => 450.00],
            ['nombre' => 'Rectificado de Cigüeñal', 'descripcion' => 'Rectificación y balanceo del cigüeñal', 'costo' => 1600.00],
            ['nombre' => 'Cambio de Pistones', 'descripcion' => 'Reemplazo de pistones y montaje', 'costo' => 1400.00],
            ['nombre' => 'Cambio de Cojinetes Principales', 'descripcion' => 'Sustitución de cojinetes principales del motor', 'costo' => 900.00],
            ['nombre' => 'Cambio de Retenes de Aceite', 'descripcion' => 'Reemplazo de retenes frontales y traseros', 'costo' => 200.00],
            ['nombre' => 'Encamisado y Rectificado', 'descripcion' => 'Encamisado combinado con rectificado de cilindros', 'costo' => 2200.00],
            ['nombre' => 'Lapeo de Válvulas', 'descripcion' => 'Lapeo y asiento de válvulas en culata', 'costo' => 300.00],
            ['nombre' => 'Reparación de Culata', 'descripcion' => 'Rectificado y reparación de culata, verificación de fisuras', 'costo' => 1100.00],
            ['nombre' => 'Cambio de Junta de Culata', 'descripcion' => 'Sustitución completa de junta y montaje', 'costo' => 650.00],
            ['nombre' => 'Reacondicionado de Árbol de Levas', 'descripcion' => 'Rectificado y ajuste de árbol de levas', 'costo' => 1300.00],
            ['nombre' => 'Reparación de Segmentos', 'descripcion' => 'Reemplazo/rectificado de segmentos y pistas', 'costo' => 500.00],
            ['nombre' => 'Balanceo Dinámico Cigüeñal', 'descripcion' => 'Balanceo dinámico del conjunto cigüeñal/volante', 'costo' => 1500.00],
            ['nombre' => 'Cambio de Biela y Pistón', 'descripcion' => 'Sustitución de biela, pistón y montaje completo', 'costo' => 1700.00],
            ['nombre' => 'Reparación de PMS (Punta de Motor)', 'descripcion' => 'Reparación de componentes superiores del motor', 'costo' => 800.00],
            ['nombre' => 'Rectificado de Camisas', 'descripcion' => 'Rectificación fina de camisas de cilindros', 'costo' => 900.00],
            ['nombre' => 'Reparación de Inyectores y Bomba', 'descripcion' => 'Reparación de inyectores y bomba de combustible', 'costo' => 700.00],
            ['nombre' => 'Calado y Sincronización', 'descripcion' => 'Calado de distribución y sincronización del motor', 'costo' => 400.00],
            ['nombre' => 'Reacondicionado y Montaje de Motor', 'descripcion' => 'Reconstrucción completa y montaje del motor', 'costo' => 5500.00],
        ];

        foreach ($servicios as $s) {
            Servicio::create($s);
        }
    }
}
