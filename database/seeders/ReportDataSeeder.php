<?php

namespace Database\Seeders;

use App\Models\Cita;
use App\Models\Diagnostico;
use App\Models\OrdenTrabajo;
use App\Models\OrdenServicio;
use App\Models\Pago;
use App\Models\User;
use App\Models\Vehiculo;
use App\Models\Servicio;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ReportDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener usuarios
        $clientes = User::where('tipo', 'cliente')->get();
        $mecanicos = User::where('tipo', 'mecanico')->get();
        $vehiculos = Vehiculo::all();
        $servicios = Servicio::all();

        if ($clientes->isEmpty() || $mecanicos->isEmpty() || $vehiculos->isEmpty()) {
            $this->command->error('Por favor, ejecuta los seeders base primero: UserSeeder y VehiculoSeeder');
            return;
        }

        $this->command->info('Generando datos de citas, diagnósticos, órdenes y pagos para 2025...');

        // Datos para generar variedad
        $motivos = [
            'Revisión general',
            'Cambio de aceite',
            'Reparación de frenos',
            'Problema en motor',
            'Alineación y balanceo',
            'Reparación de transmisión',
            'Problema eléctrico',
            'Mantenimiento preventivo',
            'Reparación de suspensión',
            'Cambio de llantas',
        ];

        $estadosCita = ['pendiente', 'confirmada', 'completada', 'cancelada'];
        $estadosDiagnostico = ['en_revision', 'completado', 'aprobado_cliente', 'rechazado_cliente'];
        $estadosOrden = ['presupuestada', 'aprobada', 'en_proceso', 'completada', 'entregada', 'cancelada'];
        $tipoPago = ['contado', 'credito'];
        $estadoPago = ['pendiente', 'pagado_parcial', 'pagado_total', 'vencido'];

        // Generar aproximadamente 150-200 registros para todo el año
        $diasDelAnio = 365;
        $citasPorDia = 0.4; // Aproximadamente 2 citas por 5 días
        $citasEsperadas = ceil($diasDelAnio * $citasPorDia);

        $this->command->info("Generando aproximadamente {$citasEsperadas} citas para 2025...");

        $citasCreadas = 0;
        $diagnosticosCreados = 0;
        $ordenesCreadas = 0;
        $pagosCreados = 0;

        // Iterar por cada día del 2025
        $fecha = Carbon::create(2025, 1, 1);
        $fin = Carbon::create(2025, 12, 31);

        while ($fecha <= $fin) {
            // 40% de probabilidad de tener citas este día
            if (rand(1, 10) <= 4) {
                $numerosCitas = rand(1, 3); // 1 a 3 citas por día
                
                for ($i = 0; $i < $numerosCitas; $i++) {
                    // Seleccionar cliente y vehículo aleatorio
                    $cliente = $clientes->random();
                    $vehiculo = $vehiculos->random();
                    
                    // Generar hora aleatoria (8 AM a 5 PM)
                    $hora = sprintf('%02d:00:00', rand(8, 17));
                    
                    // Crear cita con código único
                    $citasCreadas++;
                    $cita = Cita::create([
                        'codigo' => 'CIT-' . str_pad($citasCreadas, 6, '0', STR_PAD_LEFT),
                        'cliente_id' => $cliente->id,
                        'vehiculo_id' => $vehiculo->id,
                        'fecha' => $fecha->copy(),
                        'hora' => $hora,
                        'motivo' => $motivos[array_rand($motivos)],
                        'estado' => $estadosCita[array_rand($estadosCita)],
                        'observaciones' => 'Cita generada automáticamente para reportes',
                        'created_at' => $fecha->copy()->setTimeFromTimeString($hora),
                        'updated_at' => $fecha->copy()->setTimeFromTimeString($hora),
                    ]);

                    // 70% de probabilidad de que la cita tenga diagnóstico
                    if (rand(1, 100) <= 70) {
                        $mecanico = $mecanicos->random();
                        $diagnosticosCreados++;
                        
                        $diagnostico = Diagnostico::create([
                            'codigo' => 'DIA-' . str_pad($diagnosticosCreados, 6, '0', STR_PAD_LEFT),
                            'cita_id' => $cita->id,
                            'mecanico_id' => $mecanico->id,
                            'fecha_diagnostico' => $fecha->copy(),
                            'descripcion_problema' => $cita->motivo,
                            'diagnostico' => 'Se encontró el problema: ' . $cita->motivo,
                            'recomendaciones' => 'Se recomienda reparación inmediata',
                            'estado' => $estadosDiagnostico[array_rand($estadosDiagnostico)],
                            'created_at' => $fecha->copy()->setTimeFromTimeString($hora),
                            'updated_at' => $fecha->copy()->setTimeFromTimeString($hora),
                        ]);
                        
                        $diagnosticosCreados++;

                        // 80% de probabilidad de que haya orden de trabajo
                        if (rand(1, 100) <= 80) {
                            $mecanico = $mecanicos->random();
                            $fechaInicio = $fecha->copy()->addDays(rand(0, 3));
                            $fechaFin = $fechaInicio->copy()->addDays(rand(1, 7));
                            
                            $costoManoObra = rand(500, 3000); // En la divisa local
                            $costoRepuestos = rand(0, 2000);
                            $montoTotal = $costoManoObra + $costoRepuestos;
                            
                            $ordenesCreadas++;
                            $orden = OrdenTrabajo::create([
                                'codigo' => 'ORD-' . str_pad($ordenesCreadas, 6, '0', STR_PAD_LEFT),
                                'diagnostico_id' => $diagnostico->id,
                                'mecanico_id' => $mecanico->id,
                                'fecha_creacion' => $fecha->copy(),
                                'fecha_inicio' => $fechaInicio,
                                'fecha_fin_estimada' => $fechaFin,
                                'fecha_fin_real' => rand(1, 100) <= 70 ? $fechaFin->copy()->addDays(rand(-2, 2)) : null,
                                'costo_mano_obra' => $costoManoObra,
                                'costo_repuestos' => $costoRepuestos,
                                'estado' => $estadosOrden[array_rand($estadosOrden)],
                                'observaciones' => 'Orden generada automáticamente',
                                'created_at' => $fecha->copy()->setTimeFromTimeString($hora),
                                'updated_at' => $fecha->copy()->setTimeFromTimeString($hora),
                            ]);
                            
                        // Agregar servicios a la orden (1 a 4 servicios)
                        if (!$servicios->isEmpty()) {
                            $numeroServicios = rand(1, min(4, $servicios->count()));
                            $serviciosAleatorios = $servicios->random($numeroServicios);
                            
                            foreach ($serviciosAleatorios as $servicio) {
                                OrdenServicio::create([
                                    'orden_trabajo_id' => $orden->id,
                                    'servicio_id' => $servicio->id,
                                    'cantidad' => rand(1, 3),
                                    'precio_unitario' => $servicio->precio_base,
                                    'descripcion' => 'Servicio realizado',
                                ]);
                            }
                        }                            // Crear pagos para la orden (1 a 3 pagos)
                            $numeroPagos = rand(1, 3);
                            $montoPorPago = floor($montoTotal / $numeroPagos);
                            $montoRestante = $montoTotal;
                            
                            for ($p = 0; $p < $numeroPagos; $p++) {
                                $montoEste = ($p === $numeroPagos - 1) ? $montoRestante : $montoPorPago;
                                $montoPagado = 0;
                                $estado = 'pendiente';
                                $cuotasPagadas = 0;
                                
                                // 80% de probabilidad de que esté pagado
                                if (rand(1, 100) <= 80) {
                                    $montoPagado = $montoEste;
                                    $estado = 'pagado_total';
                                    $cuotasPagadas = 1;
                                } else if (rand(1, 100) <= 50) {
                                    $montoPagado = rand((int)($montoEste * 0.1), (int)($montoEste * 0.9));
                                    $estado = 'pagado_parcial';
                                    $cuotasPagadas = 0;
                                } else {
                                    $montoPagado = 0;
                                    $estado = 'pendiente';
                                    $cuotasPagadas = 0;
                                }
                                
                                $fechaPago = $fechaFin->copy()->addDays(rand(0, 10));
                                $pagosCreados++;
                                
                                // Determinar si es al contado o crédito
                                $tipoEste = $numeroPagos === 1 ? 'contado' : 'credito';
                                
                                Pago::create([
                                    'codigo' => 'PAG-' . str_pad($pagosCreados, 6, '0', STR_PAD_LEFT),
                                    'orden_trabajo_id' => $orden->id,
                                    'monto_total' => $montoEste,
                                    'monto_pagado' => $montoPagado,
                                    'tipo_pago' => $tipoEste,
                                    'numero_cuotas' => $numeroPagos,
                                    'cuotas_pagadas' => $cuotasPagadas,
                                    'estado' => $estado,
                                    'fecha_vencimiento' => $fechaPago->copy()->addDays(15),
                                    'observaciones' => 'Pago ' . ($p + 1) . ' de ' . $numeroPagos,
                                    'created_at' => $fechaPago,
                                    'updated_at' => $fechaPago,
                                ]);
                                
                                $montoRestante -= $montoEste;
                            }
                        }
                    }
                }
            }

            $fecha->addDay();
        }

        $this->command->info("✓ Citas creadas: {$citasCreadas}");
        $this->command->info("✓ Diagnósticos creados: {$diagnosticosCreados}");
        $this->command->info("✓ Órdenes de trabajo creadas: {$ordenesCreadas}");
        $this->command->info("✓ Pagos creados: {$pagosCreados}");
        $this->command->info('¡Datos de reportes generados exitosamente!');
    }
}
