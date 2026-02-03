<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenTrabajo extends Model
{
    use HasFactory;

    protected $table = 'ordenes_trabajo';

    protected $fillable = [
        'codigo',
        'diagnostico_id',
        'mecanico_id',
        'fecha_creacion',
        'fecha_inicio',
        'fecha_fin_estimada',
        'fecha_fin_real',
        'costo_mano_obra',
        'costo_repuestos',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'fecha_creacion' => 'date',
        'fecha_inicio' => 'date',
        'fecha_fin_estimada' => 'date',
        'fecha_fin_real' => 'date',
        'costo_mano_obra' => 'decimal:2',
        'costo_repuestos' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // IMPORTANTE: Agregar estos atributos al appends
    protected $appends = ['total', 'progreso'];

    // =========================================================================
    // RELACIONES
    // =========================================================================

    /**
     * Relación con el diagnóstico
     */
    public function diagnostico()
    {
        return $this->belongsTo(Diagnostico::class);
    }

    /**
     * Relación con el mecánico asignado
     */
    public function mecanico()
    {
        return $this->belongsTo(User::class, 'mecanico_id');
    }

    /**
     * Relación con los servicios de la orden
     */
    public function servicios()
    {
        return $this->hasMany(OrdenServicio::class, 'orden_trabajo_id');
    }

    /**
     * Relación con los pagos asociados
     */
    public function pagos()
    {
        return $this->hasMany(Pago::class, 'orden_trabajo_id');
    }

    /**
     * Relación con el cliente a través del diagnóstico y cita
     */
    public function cliente()
    {
        return $this->hasOneThrough(
            User::class,
            Diagnostico::class,
            'id', // Foreign key on diagnosticos table
            'id', // Foreign key on usuarios table
            'diagnostico_id', // Local key on ordenes_trabajo table
            'cita_id' // Local key on diagnosticos table
        )->via('diagnostico.cita');
    }

    /**
     * Relación con el vehículo a través del diagnóstico y cita
     */
    public function vehiculo()
    {
        return $this->hasOneThrough(
            Vehiculo::class,
            Diagnostico::class,
            'id', // Foreign key on diagnosticos table
            'id', // Foreign key on vehiculos table
            'diagnostico_id', // Local key on ordenes_trabajo table
            'cita_id' // Local key on diagnosticos table
        )->via('diagnostico.cita');
    }

    // =========================================================================
    // ACCESSORS - DEBEN IR ANTES DE LOS SCOPES
    // =========================================================================

    /**
     * Calcular el total de la orden (mano de obra + repuestos)
     */
    public function getTotalAttribute()
    {
        $costoManoObra = (float) ($this->attributes['costo_mano_obra'] ?? 0);
        $costoRepuestos = (float) ($this->attributes['costo_repuestos'] ?? 0);
        return $costoManoObra + $costoRepuestos;
    }

    /**
     * Obtener el progreso de la orden en porcentaje
     */
    public function getProgresoAttribute()
    {
        $estados = [
            'presupuestada' => 25,
            'aprobada' => 50,
            'en_proceso' => 75,
            'completada' => 90,
            'entregada' => 100,
            'cancelada' => 0,
        ];

        return $estados[$this->estado] ?? 0;
    }

    /**
     * Calcular el total de servicios
     */
    public function getTotalServiciosAttribute()
    {
        return $this->servicios->sum('subtotal');
    }

    /**
     * Obtener información resumida de la orden
     */
    public function getInfoResumidaAttribute()
    {
        return [
            'codigo' => $this->codigo,
            'cliente' => $this->cliente->nombre ?? 'N/A',
            'vehiculo' => $this->vehiculo->marca . ' ' . $this->vehiculo->modelo ?? 'N/A',
            'mecanico' => $this->mecanico->nombre,
            'estado' => $this->estado,
            'total' => $this->total,
            'fecha_creacion' => $this->fecha_creacion->format('d/m/Y'),
        ];
    }

    /**
     * Obtener el texto legible del estado
     */
    public function getEstadoTextoAttribute()
    {
        $estados = self::getEstadosDisponibles();
        return $estados[$this->estado] ?? $this->estado;
    }

    /**
     * Obtener el pago principal de la orden
     */
    public function getPagoPrincipalAttribute()
    {
        return $this->pagos()->first();
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    /**
     * Scope para órdenes presupuestadas
     */
    public function scopePresupuestadas($query)
    {
        return $query->where('estado', 'presupuestada');
    }

    /**
     * Scope para órdenes aprobadas
     */
    public function scopeAprobadas($query)
    {
        return $query->where('estado', 'aprobada');
    }

    /**
     * Scope para órdenes en proceso
     */
    public function scopeEnProceso($query)
    {
        return $query->where('estado', 'en_proceso');
    }

    /**
     * Scope para órdenes completadas
     */
    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'completada');
    }

    /**
     * Scope para órdenes entregadas
     */
    public function scopeEntregadas($query)
    {
        return $query->where('estado', 'entregada');
    }

    /**
     * Scope para órdenes canceladas
     */
    public function scopeCanceladas($query)
    {
        return $query->where('estado', 'cancelada');
    }

    /**
     * Scope para órdenes de un mecánico específico
     */
    public function scopeDelMecanico($query, $mecanicoId)
    {
        return $query->where('mecanico_id', $mecanicoId);
    }

    /**
     * Scope para órdenes del mes actual
     */
    public function scopeDelMesActual($query)
    {
        return $query->whereYear('fecha_creacion', now()->year)
                    ->whereMonth('fecha_creacion', now()->month);
    }

    /**
     * Scope para órdenes vencidas (fecha fin estimada pasada)
     */
    public function scopeVencidas($query)
    {
        return $query->where('fecha_fin_estimada', '<', now())
                    ->whereIn('estado', ['presupuestada', 'aprobada', 'en_proceso']);
    }

    /**
     * Scope para órdenes activas (no completadas ni canceladas)
     */
    public function scopeActivas($query)
    {
        return $query->whereIn('estado', ['presupuestada', 'aprobada', 'en_proceso']);
    }

    // =========================================================================
    // MÉTODOS DE UTILIDAD
    // =========================================================================

    /**
     * Verificar si la orden está presupuestada
     */
    public function estaPresupuestada()
    {
        return $this->estado === 'presupuestada';
    }

    /**
     * Verificar si la orden está aprobada
     */
    public function estaAprobada()
    {
        return $this->estado === 'aprobada';
    }

    /**
     * Verificar si la orden está en proceso
     */
    public function estaEnProceso()
    {
        return $this->estado === 'en_proceso';
    }

    /**
     * Verificar si la orden está completada
     */
    public function estaCompletada()
    {
        return $this->estado === 'completada';
    }

    /**
     * Verificar si la orden está entregada
     */
    public function estaEntregada()
    {
        return $this->estado === 'entregada';
    }

    /**
     * Verificar si la orden está cancelada
     */
    public function estaCancelada()
    {
        return $this->estado === 'cancelada';
    }

    /**
     * Marcar orden como aprobada
     */
    public function marcarComoAprobada()
    {
        $this->update([
            'estado' => 'aprobada',
            'fecha_inicio' => now(),
        ]);
        return $this;
    }

    /**
     * Marcar orden como en proceso
     */
    public function marcarComoEnProceso()
    {
        $this->update(['estado' => 'en_proceso']);
        return $this;
    }

    /**
     * Marcar orden como completada
     */
    public function marcarComoCompletada()
    {
        $this->update([
            'estado' => 'completada',
            'fecha_fin_real' => now(),
        ]);
        return $this;
    }

    /**
     * Marcar orden como entregada
     */
    public function marcarComoEntregada()
    {
        $this->update(['estado' => 'entregada']);
        return $this;
    }

    /**
     * Marcar orden como cancelada
     */
    public function marcarComoCancelada()
    {
        $this->update(['estado' => 'cancelada']);
        return $this;
    }

    /**
     * Verificar si la orden está vencida
     */
    public function estaVencida()
    {
        return $this->fecha_fin_estimada &&
               $this->fecha_fin_estimada < now() &&
               in_array($this->estado, ['presupuestada', 'aprobada', 'en_proceso']);
    }

    /**
     * Agregar un servicio a la orden
     */
    public function agregarServicio($servicioId, $cantidad = 1, $precioUnitario = null, $descripcion = null)
    {
        $servicio = Servicio::find($servicioId);

        return $this->servicios()->create([
            'servicio_id' => $servicioId,
            'descripcion' => $descripcion ?? $servicio->nombre,
            'cantidad' => $cantidad,
            'precio_unitario' => $precioUnitario ?? $servicio->precio_base,
        ]);
    }

    /**
     * Actualizar costos de la orden
     */
    public function actualizarCostos()
    {
        $totalServicios = $this->servicios->sum('subtotal');

        $this->update([
            'costo_repuestos' => $totalServicios,
        ]);

        return $this;
    }

    /**
     * Verificar si la orden tiene pagos asociados
     */
    public function tienePagos()
    {
        return $this->pagos()->exists();
    }

    /**
     * Verificar si la orden está completamente pagada
     */
    public function estaPagadaCompletamente()
    {
        $pago = $this->pagoPrincipal;
        return $pago && $pago->estado === 'pagado_total';
    }

    /**
     * Buscar órdenes por código, cliente o vehículo
     */
    public static function buscar($termino)
    {
        return self::where('codigo', 'LIKE', "%{$termino}%")
                  ->orWhereHas('diagnostico.cita.cliente', function($query) use ($termino) {
                      $query->where('nombre', 'LIKE', "%{$termino}%");
                  })
                  ->orWhereHas('diagnostico.cita.vehiculo', function($query) use ($termino) {
                      $query->where('marca', 'LIKE', "%{$termino}%")
                            ->orWhere('modelo', 'LIKE', "%{$termino}%")
                            ->orWhere('placa', 'LIKE', "%{$termino}%");
                  })
                  ->get();
    }

    /**
     * Obtener estadísticas de órdenes
     */
    public static function obtenerEstadisticas($fechaInicio = null, $fechaFin = null)
    {
        $query = self::query();

        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('fecha_creacion', [$fechaInicio, $fechaFin]);
        }

        return [
            'total' => $query->count(),
            'presupuestadas' => (clone $query)->presupuestadas()->count(),
            'aprobadas' => (clone $query)->aprobadas()->count(),
            'en_proceso' => (clone $query)->enProceso()->count(),
            'completadas' => (clone $query)->completadas()->count(),
            'entregadas' => (clone $query)->entregadas()->count(),
            'ingresos_totales' => (clone $query)->completadas()->get()->sum('total'),
        ];
    }

    /**
     * Obtener el atributo esta_presupuestada para el frontend
     */
    public function getEstaPresupuestadaAttribute()
    {
        return $this->estaPresupuestada();
    }
    public static function getEstadosDisponibles()
    {
        return [
            'presupuestada' => 'Presupuestada',
            'aprobada' => 'Aprobada',
            'en_proceso' => 'En Proceso',
            'completada' => 'Completada',
            'entregada' => 'Entregada',
            'cancelada' => 'Cancelada',
        ];
    }
    
}
