<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $table = 'pagos';

    protected $fillable = [
        'codigo',
        'orden_trabajo_id',
        'monto_total',
        'monto_pagado',
        'tipo_pago',
        'numero_cuotas',
        'cuotas_pagadas',
        'estado',
        'fecha_vencimiento',
        'observaciones',
    ];

    protected $casts = [
        'monto_total' => 'decimal:2',
        'monto_pagado' => 'decimal:2',
        'monto_pendiente' => 'decimal:2',
        'fecha_vencimiento' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // =========================================================================
    // RELACIONES
    // =========================================================================

    /**
     * Relación con la orden de trabajo
     */
    public function ordenTrabajo()
    {
        return $this->belongsTo(OrdenTrabajo::class);
    }

    /**
     * Relación con los detalles de pago
     */
    public function detalles()
    {
        return $this->hasMany(PagoDetalle::class);
    }

    /**
     * Relación con el cliente a través de la orden de trabajo
     */
    public function cliente()
    {
        return $this->hasOneThrough(
            User::class,
            OrdenTrabajo::class,
            'id', // Foreign key on ordenes_trabajo table
            'id', // Foreign key on usuarios table
            'orden_trabajo_id', // Local key on pagos table
            'diagnostico_id' // Local key on ordenes_trabajo table
        )->via('ordenTrabajo.diagnostico.cita');
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    /**
     * Scope para pagos pendientes
     */
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    /**
     * Scope para pagos pagados parcialmente
     */
    public function scopePagadosParcialmente($query)
    {
        return $query->where('estado', 'pagado_parcial');
    }

    /**
     * Scope para pagos pagados totalmente
     */
    public function scopePagadosTotalmente($query)
    {
        return $query->where('estado', 'pagado_total');
    }

    /**
     * Scope para pagos vencidos
     */
    public function scopeVencidos($query)
    {
        return $query->where('estado', 'vencido');
    }

    /**
     * Scope para pagos al contado
     */
    public function scopeAlContado($query)
    {
        return $query->where('tipo_pago', 'contado');
    }

    /**
     * Scope para pagos a crédito
     */
    public function scopeAlCredito($query)
    {
        return $query->where('tipo_pago', 'credito');
    }

    /**
     * Scope para pagos vencidos (fecha vencimiento pasada)
     */
    public function scopeConVencimientoPasado($query)
    {
        return $query->where('fecha_vencimiento', '<', now())
                    ->whereIn('estado', ['pendiente', 'pagado_parcial']);
    }

    /**
     * Scope para pagos del mes actual
     */
    public function scopeDelMesActual($query)
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    // =========================================================================
    // MÉTODOS DE UTILIDAD
    // =========================================================================

    /**
     * Verificar si el pago está pendiente
     */
    public function estaPendiente()
    {
        return $this->estado === 'pendiente';
    }

    /**
     * Verificar si el pago está pagado parcialmente
     */
    public function estaPagadoParcialmente()
    {
        return $this->estado === 'pagado_parcial';
    }

    /**
     * Verificar si el pago está pagado totalmente
     */
    public function estaPagadoTotalmente()
    {
        return $this->estado === 'pagado_total';
    }

    /**
     * Verificar si el pago está vencido
     */
    public function estaVencido()
    {
        return $this->estado === 'vencido';
    }

    /**
     * Verificar si el pago es al contado
     */
    public function esAlContado()
    {
        return $this->tipo_pago === 'contado';
    }

    /**
     * Verificar si el pago es a crédito
     */
    public function esAlCredito()
    {
        return $this->tipo_pago === 'credito';
    }

    /**
     * Verificar si el pago tiene fecha de vencimiento pasada
     */
    public function tieneVencimientoPasado()
    {
        return $this->fecha_vencimiento && $this->fecha_vencimiento < now();
    }

    /**
     * Calcular el monto pendiente
     */
    public function getMontoPendienteCalculadoAttribute()
    {
        return $this->monto_total - $this->monto_pagado;
    }

    /**
     * Calcular el porcentaje pagado
     */
    public function getPorcentajePagadoAttribute()
    {
        if ($this->monto_total == 0) return 0;
        return ($this->monto_pagado / $this->monto_total) * 100;
    }

    /**
     * Obtener información resumida del pago
     */
    public function getInfoResumidaAttribute()
    {
        return [
            'codigo' => $this->codigo,
            'cliente' => $this->cliente->nombre ?? 'N/A',
            'monto_total' => $this->monto_total,
            'monto_pagado' => $this->monto_pagado,
            'monto_pendiente' => $this->monto_pendiente,
            'tipo_pago' => $this->tipo_pago,
            'estado' => $this->estado,
            'fecha_vencimiento' => $this->fecha_vencimiento?->format('d/m/Y'),
        ];
    }

    /**
     * Obtener los estados disponibles para un pago
     */
    public static function getEstadosDisponibles()
    {
        return [
            'pendiente' => 'Pendiente',
            'pagado_parcial' => 'Pagado Parcialmente',
            'pagado_total' => 'Pagado Totalmente',
            'vencido' => 'Vencido',
        ];
    }

    /**
     * Obtener los tipos de pago disponibles
     */
    public static function getTiposPagoDisponibles()
    {
        return [
            'contado' => 'Al Contado',
            'credito' => 'Al Crédito',
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
     * Obtener el texto legible del tipo de pago
     */
    public function getTipoPagoTextoAttribute()
    {
        $tipos = self::getTiposPagoDisponibles();
        return $tipos[$this->tipo_pago] ?? $this->tipo_pago;
    }

    /**
     * Registrar un pago
     */
    public function registrarPago($monto, $metodoPago, $numeroComprobante = null, $banco = null, $referencia = null, $recibidoPor = null)
    {
        // Crear el detalle del pago
        $detalle = $this->detalles()->create([
            'numero_cuota' => $this->cuotas_pagadas + 1,
            'monto' => $monto,
            'metodo_pago' => $metodoPago,
            'numero_comprobante' => $numeroComprobante,
            'banco' => $banco,
            'referencia' => $referencia,
            'recibido_por' => $recibidoPor,
            'fecha_pago' => now()->format('Y-m-d'),
            'hora_pago' => now()->format('H:i:s'),
        ]);

        // Actualizar el estado del pago (se hace automáticamente por el trigger de la BD)

        return $detalle;
    }

    /**
     * Calcular el monto de cada cuota (para créditos)
     */
    public function getMontoCuotaAttribute()
    {
        if ($this->esAlCredito() && $this->numero_cuotas > 0) {
            return $this->monto_total / $this->numero_cuotas;
        }
        return $this->monto_total;
    }

    /**
     * Obtener las cuotas pendientes
     */
    public function getCuotasPendientesAttribute()
    {
        return $this->numero_cuotas - $this->cuotas_pagadas;
    }

    /**
     * Obtener el próximo número de cuota
     */
    public function getProximaCuotaAttribute()
    {
        return $this->cuotas_pagadas + 1;
    }

    /**
     * Verificar si todas las cuotas están pagadas
     */
    public function todasLasCuotasPagadas()
    {
        return $this->cuotas_pagadas >= $this->numero_cuotas;
    }

    /**
     * Generar plan de pagos (para créditos)
     */
    public function generarPlanPagos()
    {
        if (!$this->esAlCredito() || !$this->fecha_vencimiento) {
            return [];
        }

        $plan = [];
        $montoCuota = $this->monto_cuota;
        $fechaBase = $this->fecha_vencimiento;

        for ($i = 1; $i <= $this->numero_cuotas; $i++) {
            $fechaVencimiento = $fechaBase->copy()->addMonths($i - 1);

            $plan[] = [
                'numero_cuota' => $i,
                'monto' => $montoCuota,
                'fecha_vencimiento' => $fechaVencimiento->format('d/m/Y'),
                'pagada' => $i <= $this->cuotas_pagadas,
                'fecha_pago' => $i <= $this->cuotas_pagadas ?
                    $this->detalles->where('numero_cuota', $i)->first()?->fecha_pago?->format('d/m/Y') : null,
            ];
        }

        return $plan;
    }

    /**
     * Marcar pago como vencido
     */
    public function marcarComoVencido()
    {
        if ($this->tieneVencimientoPasado() && !$this->estaPagadoTotalmente()) {
            $this->update(['estado' => 'vencido']);
        }
        return $this;
    }

    /**
     * Buscar pagos por código, cliente o orden
     */
    public static function buscar($termino)
    {
        return self::where('codigo', 'LIKE', "%{$termino}%")
                  ->orWhereHas('ordenTrabajo.diagnostico.cita.cliente', function($query) use ($termino) {
                      $query->where('nombre', 'LIKE', "%{$termino}%");
                  })
                  ->orWhereHas('ordenTrabajo', function($query) use ($termino) {
                      $query->where('codigo', 'LIKE', "%{$termino}%");
                  })
                  ->get();
    }

    /**
     * Obtener estadísticas de pagos
     */
    public static function obtenerEstadisticas($fechaInicio = null, $fechaFin = null)
    {
        $query = self::query();

        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
        }

        return [
            'total_pagos' => $query->count(),
            'pagos_pendientes' => $query->clone()->pendientes()->count(),
            'pagos_parciales' => $query->clone()->pagadosParcialmente()->count(),
            'pagos_completos' => $query->clone()->pagadosTotalmente()->count(),
            'pagos_vencidos' => $query->clone()->vencidos()->count(),
            'ingresos_totales' => $query->clone()->sum('monto_pagado'),
            'pendiente_cobrar' => $query->clone()->sum('monto_pendiente'),
        ];
    }
}
