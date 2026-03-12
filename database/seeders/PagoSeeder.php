<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\OrdenTrabajo;
use App\Models\PlanPago;
use App\Models\Pago;
use App\Models\Factura;
use Carbon\Carbon;

class PagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ordenes = OrdenTrabajo::whereNotNull('total')
            ->where('total', '>', 0)
            ->get();

        if ($ordenes->isEmpty()) {
            $this->command->warn('No hay órdenes de trabajo disponibles. Ejecuta OrdenTrabajoSeeder primero.');
            return;
        }

        // Seleccionar aleatoriamente 30-40 órdenes para crear planes de pago
        $ordenesConPlan = $ordenes->random(min(35, $ordenes->count()));

        $metodospago = ['efectivo', 'tarjeta'];
        $estadosPago = ['pendiente', 'terminado'];
        $estadosPlan = ['pendiente', 'en proceso', 'terminado'];

        foreach ($ordenesConPlan as $orden) {
            // Determinar número aleatorio de cuotas (1 a 6)
            $numeroCuotas = rand(1, 6);
            
            // Crear el plan de pago para la orden
            $estadoPlan = $estadosPlan[array_rand($estadosPlan)];
            
            $planPago = PlanPago::create([
                'estado' => $estadoPlan,
                'fechainicio' => Carbon::parse($orden->fechainicio)->addDays(rand(1, 5))->toDateString(),
                'fechafin' => $estadoPlan === 'terminado' 
                    ? Carbon::parse($orden->fechainicio)->addDays(rand(30, 90))->toDateString() 
                    : null,
                'montototal' => $orden->total,
                'numerocuotas' => $numeroCuotas,
                'montoporcuota' => round($orden->total / $numeroCuotas, 2),
                'observacion' => rand(0, 1) ? 'Plan de pago generado automáticamente' : null,
                'orden_trabajo_id' => $orden->id,
            ]);

            // Crear los pagos según el estado del plan
            $pagosACrear = $numeroCuotas;
            
            if ($estadoPlan === 'pendiente') {
                // Si está pendiente, crear solo 0 o 1 pago
                $pagosACrear = rand(0, 1);
            } elseif ($estadoPlan === 'en proceso') {
                // Si está en proceso, crear entre 1 y numeroCuotas-1
                $pagosACrear = rand(1, max(1, $numeroCuotas - 1));
            }
            // Si está terminado, crear todos los pagos ($pagosACrear = $numeroCuotas)

            for ($i = 1; $i <= $pagosACrear; $i++) {
                // Determinar estado del pago
                $estadoPago = 'pendiente';
                if ($estadoPlan === 'terminado' || ($estadoPlan === 'en proceso' && $i < $pagosACrear)) {
                    $estadoPago = 'terminado';
                } elseif ($estadoPlan === 'en proceso' && rand(0, 1)) {
                    $estadoPago = 'terminado';
                }

                $fechaPago = Carbon::parse($planPago->fechainicio)
                    ->addMonths($i - 1)
                    ->addDays(rand(0, 10))
                    ->toDateString();

                $metodoPago = $metodospago[array_rand($metodospago)];

                // Ajustar el monto de la última cuota si es necesario
                $montoCuota = $planPago->montoporcuota;
                if ($i === $numeroCuotas) {
                    // Calcular el monto restante para evitar diferencias por redondeo
                    $totalPagado = $planPago->montoporcuota * ($numeroCuotas - 1);
                    $montoCuota = round($planPago->montototal - $totalPagado, 2);
                }

                $pago = Pago::create([
                    'estado' => $estadoPago,
                    'fechapago' => $fechaPago,
                    'metodopago' => $metodoPago,
                    'monto' => $montoCuota,
                    'numerocuota' => $i,
                    'referencia' => $estadoPago === 'terminado' 
                        ? 'REF-' . strtoupper(substr(md5($orden->id . '-' . $i), 0, 10)) 
                        : null,
                    'plan_pago_id' => $planPago->id,
                ]);

                // Crear factura para algunos pagos terminados (60% de probabilidad)
                if ($estadoPago === 'terminado' && rand(0, 100) < 60) {
                    $fechaEmision = Carbon::parse($fechaPago)->addDays(rand(0, 2))->toDateString();
                    
                    Factura::create([
                        'descripcion' => "Factura por cuota {$i}/{$numeroCuotas} - Orden #{$orden->id}",
                        'estado' => 'emitida',
                        'fechaemision' => $fechaEmision,
                        'montototal' => $montoCuota,
                        'nroautorizacion' => 'A-' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT),
                        'numerofactura' => date('Y') . '-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                        'pago_id' => $pago->id,
                    ]);
                }
            }

            // Actualizar el estado del plan según los pagos creados
            if ($estadoPlan === 'terminado' && $pagosACrear === $numeroCuotas) {
                $planPago->update(['fechafin' => $fechaPago ?? now()->toDateString()]);
            }
        }

        $this->command->info("✓ Se crearon planes de pago para {$ordenesConPlan->count()} órdenes de trabajo");
        
        $totalPlanes = PlanPago::count();
        $totalPagos = Pago::count();
        $totalFacturas = Factura::count();
        
        $this->command->info("✓ Total de planes de pago: {$totalPlanes}");
        $this->command->info("✓ Total de pagos: {$totalPagos}");
        $this->command->info("✓ Total de facturas emitidas: {$totalFacturas}");
    }
}
