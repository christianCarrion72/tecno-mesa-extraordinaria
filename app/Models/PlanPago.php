<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DateTimeInterface;

class PlanPago extends Model
{
    use SoftDeletes;

    protected $table = 'plan_pagos';

    protected $fillable = [
        'estado',
        'fechainicio',
        'fechafin',
        'montoporcuota',
        'montototal',
        'numerocuotas',
        'observacion',
        'orden_trabajo_id',
    ];

    protected $casts = [
        'fechainicio' => 'date',
        'fechafin' => 'date',
        'montoporcuota' => 'float',
        'montototal' => 'float',
    ];

    public function ordenTrabajo()
    {
        return $this->belongsTo(OrdenTrabajo::class);
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }

    public static function crearParaOrden(OrdenTrabajo $ordenTrabajo, array $data): self
    {
        $payload = [
            'estado' => $data['estado'] ?? 'pendiente',
            'fechainicio' => $data['fechainicio'] ?? now()->toDateString(),
            'fechafin' => $data['fechafin'] ?? null,
            'montototal' => $ordenTrabajo->total,
            'numerocuotas' => (int) ($data['numerocuotas'] ?? 1),
            'observacion' => $data['observacion'] ?? null,
            'orden_trabajo_id' => $ordenTrabajo->id,
        ];

        $payload['montoporcuota'] = isset($data['montoporcuota'])
            ? (float) $data['montoporcuota']
            : ($payload['numerocuotas'] > 0 ? $payload['montototal'] / $payload['numerocuotas'] : $payload['montototal']);

        return static::create($payload);
    }

    public function actualizarDesdeFormulario(array $data): bool
    {
        $payload = [
            'estado' => $data['estado'] ?? $this->estado,
            'fechainicio' => $data['fechainicio'] ?? $this->fechainicio,
            'fechafin' => $data['fechafin'] ?? $this->fechafin,
            'numerocuotas' => (int) ($data['numerocuotas'] ?? $this->numerocuotas),
            'observacion' => $data['observacion'] ?? $this->observacion,
        ];

        $payload['montototal'] = optional($this->ordenTrabajo)->total ?? $this->montototal;
        $payload['montoporcuota'] = isset($data['montoporcuota'])
            ? (float) $data['montoporcuota']
            : ($payload['numerocuotas'] > 0 ? $payload['montototal'] / $payload['numerocuotas'] : $payload['montototal']);

        return $this->update($payload);
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d');
    }

    public function actualizarEstadoSegunPagos(): void
    {
        $terminados = $this->pagos()->where('estado', 'terminado')->count();
        $totalReg = $this->pagos()->count();

        if ($terminados >= (int) $this->numerocuotas && $totalReg >= (int) $this->numerocuotas) {
            if ($this->estado !== 'terminado') {
                $this->update(['estado' => 'terminado']);
            }
        }
    }
}
