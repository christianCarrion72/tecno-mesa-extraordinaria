<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\OrdenTrabajo;
use App\Models\OrdenTrabajoServicio;
use App\Models\Servicio;
use App\Models\Incidencia;
use App\Models\Cliente;
use App\Models\Motor;
use App\Models\User;
use Illuminate\Support\Str;

class OrdenTrabajoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $clientes = Cliente::all();
        $motores = Motor::all();
        $servicios = Servicio::all();

        if ($users->isEmpty() || $clientes->isEmpty() || $motores->isEmpty() || $servicios->isEmpty()) {
            // safety: ensure we have base data
            $this->command->warn('Hay datos faltantes (users/clientes/motores/servicios). Asegúrate de correr seeders previos.');
            return;
        }

        // Crear 50 órdenes de trabajo
        for ($i = 0; $i < 50; $i++) {
            $cliente = $clientes->random();
            $usuario = $users->random();
            $motor = $motores->random();

            $fechainicio = now()->subDays(rand(0, 60));
            $fechafin = (rand(0, 3) === 0) ? $fechainicio->addDays(rand(1, 20))->toDateString() : null;

            $orden = OrdenTrabajo::create([
                'fechainicio' => $fechainicio->toDateString(),
                'fechafin' => $fechafin,
                'descripcion' => 'Orden automática para cliente ' . $cliente->nombre . ' - ' . Str::random(6),
                'total' => 0,
                'estado' => ['pendiente','aprobado','proceso','terminado'][array_rand(['pendiente','aprobado','proceso','terminado'])],
                'cliente_id' => $cliente->id,
                'usuario_id' => $usuario->id,
                'motor_id' => $motor->id,
            ]);

            // Asociar entre 3 y 6 servicios por orden
            $pickCount = rand(3, 6);
            $chosen = $servicios->random($pickCount);
            $total = 0;

            foreach ($chosen as $s) {
                $cantidad = rand(1, 3);
                // small random variation on price
                $precio = round($s->costo * (1 + (rand(-10, 10) / 100)), 2);
                $subtotal = round($cantidad * $precio, 2);

                OrdenTrabajoServicio::create([
                    'orden_trabajo_id' => $orden->id,
                    'servicio_id' => $s->id,
                    'cantidad' => $cantidad,
                    'precio' => $precio,
                    'subtotal' => $subtotal,
                ]);

                $total += $subtotal;
            }

            // actualizar total
            $orden->update(['total' => $total]);

            // Crear 2 incidencias por orden
            for ($j = 0; $j < 2; $j++) {
                Incidencia::create([
                    'orden_trabajo_id' => $orden->id,
                    'descripcion' => 'Incidencia automática #' . ($j+1) . ' para orden ' . $orden->id,
                    'fecha' => now()->subDays(rand(0, 30))->toDateString(),
                    'estado' => ['registrada','en revisión','resuelta'][rand(0,2)],
                ]);
            }
        }
    }
}
