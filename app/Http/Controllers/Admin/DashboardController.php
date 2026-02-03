<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Cita;
use App\Models\Vehiculo;
use App\Models\OrdenTrabajo;
use App\Models\Pago;
use App\Models\Servicio;
use App\Models\Diagnostico;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = $this->getAdminStats();

        return Inertia::render('Admin.Dashboard', [
            'stats' => $stats,
        ]);
    }

    private function getAdminStats()
    {
        return [
            // Estadísticas generales
            'total_clientes' => User::clientes()->activos()->count(),
            'total_mecanicos' => User::mecanicos()->activos()->count(),
            'total_vehiculos' => Vehiculo::activos()->count(),
            'total_servicios' => Servicio::activos()->count(),

            // Estadísticas de citas
            'citas_hoy' => Cita::whereDate('fecha', today())->count(),
            'citas_pendientes' => Cita::where('estado', 'pendiente')->count(),
            'citas_confirmadas' => Cita::where('estado', 'confirmada')->count(),
            'citas_en_proceso' => Cita::where('estado', 'en_proceso')->count(),
            'citas_completadas' => Cita::where('estado', 'completada')->count(),

            // Estadísticas de diagnósticos
            'diagnosticos_pendientes' => Diagnostico::where('estado', 'en_revision')->count(),
            'diagnosticos_completados' => Diagnostico::where('estado', 'completado')->count(),

            // Estadísticas de órdenes de trabajo
            'ordenes_presupuestadas' => OrdenTrabajo::where('estado', 'presupuestada')->count(),
            'ordenes_aprobadas' => OrdenTrabajo::where('estado', 'aprobada')->count(),
            'ordenes_en_proceso' => OrdenTrabajo::where('estado', 'en_proceso')->count(),
            'ordenes_completadas' => OrdenTrabajo::where('estado', 'completada')->count(),
            'ordenes_entregadas' => OrdenTrabajo::where('estado', 'entregada')->count(),

            // Estadísticas financieras
            'ingresos_mes' => Pago::where('estado', 'pagado_total')
                ->whereMonth('created_at', now()->month)
                ->sum('monto_total'),
            'ingresos_hoy' => Pago::where('estado', 'pagado_total')
                ->whereDate('created_at', today())
                ->sum('monto_total'),
            'pagos_pendientes' => Pago::whereIn('estado', ['pendiente', 'pagado_parcial'])->count(),

            // Datos para gráficos y listas
            'citas_por_estado' => [
                'pendiente' => Cita::where('estado', 'pendiente')->count(),
                'confirmada' => Cita::where('estado', 'confirmada')->count(),
                'en_proceso' => Cita::where('estado', 'en_proceso')->count(),
                'completada' => Cita::where('estado', 'completada')->count(),
                'cancelada' => Cita::where('estado', 'cancelada')->count(),
            ],

            // Últimas citas
            'ultimas_citas' => Cita::with(['cliente', 'vehiculo'])
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($cita) {
                    return [
                        'id' => $cita->id,
                        'codigo' => $cita->codigo,
                        'cliente' => $cita->cliente->nombre,
                        'vehiculo' => $cita->vehiculo->marca . ' ' . $cita->vehiculo->modelo,
                        'fecha' => $cita->fecha->format('d/m/Y'),
                        'hora' => $cita->hora,
                        'estado' => $cita->estado,
                    ];
                }),

            // Próximas citas de hoy
            'citas_hoy_lista' => Cita::with(['cliente', 'vehiculo'])
                ->whereDate('fecha', today())
                ->whereIn('estado', ['confirmada', 'pendiente'])
                ->orderBy('hora')
                ->take(5)
                ->get()
                ->map(function ($cita) {
                    return [
                        'id' => $cita->id,
                        'codigo' => $cita->codigo,
                        'cliente' => $cita->cliente->nombre,
                        'vehiculo' => $cita->vehiculo->placa,
                        'hora' => $cita->hora,
                        'estado' => $cita->estado,
                    ];
                }),

            // Órdenes recientes
            'ordenes_recientes' => OrdenTrabajo::with(['diagnostico.cita.cliente', 'mecanico'])
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($orden) {
                    return [
                        'id' => $orden->id,
                        'codigo' => $orden->codigo,
                        'cliente' => $orden->diagnostico->cita->cliente->nombre,
                        'mecanico' => $orden->mecanico->nombre,
                        'estado' => $orden->estado,
                        'subtotal' => $orden->subtotal,
                        'fecha_creacion' => $orden->fecha_creacion->format('d/m/Y'),
                    ];
                }),
        ];
    }
}
