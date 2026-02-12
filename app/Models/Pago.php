<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DateTimeInterface;

class Pago extends Model
{
    use SoftDeletes;

    protected $table = 'pagos';

    protected $fillable = [
        'estado',
        'fechapago',
        'metodopago',
        'monto',
        'numerocuota',
        'referencia',
        'pf_transaction_id',
        'pf_payment_method_transaction_id',
        'pf_status',
        'pf_expiration_date',
        'pf_qr_base64',
        'plan_pago_id',
    ];

    protected $casts = [
        'fechapago' => 'date',
        'monto' => 'float',
        'pf_expiration_date' => 'datetime',
    ];

    public function planPago()
    {
        return $this->belongsTo(PlanPago::class);
    }

    public function factura()
    {
        return $this->hasOne(Factura::class);
    }

    public static function crearParaPlan(PlanPago $planPago, array $data): self
    {
        $nextCuota = ($planPago->pagos()->count() ?? 0) + 1;

        $payload = [
            'estado' => $data['estado'] ?? 'pendiente',
            'fechapago' => $data['fechapago'] ?? now()->toDateString(),
            'metodopago' => $data['metodopago'] ?? 'efectivo',
            'monto' => (float) ($data['monto'] ?? $planPago->montoporcuota),
            'numerocuota' => (int) ($data['numerocuota'] ?? $nextCuota),
            'referencia' => $data['referencia'] ?? null,
            'plan_pago_id' => $planPago->id,
        ];

        return static::create($payload);
    }

    public function actualizarDesdeFormulario(array $data): bool
    {
        $payload = [
            'estado' => $data['estado'] ?? $this->estado,
            'fechapago' => $data['fechapago'] ?? $this->fechapago,
            'metodopago' => $data['metodopago'] ?? $this->metodopago,
            'monto' => isset($data['monto']) ? (float) $data['monto'] : $this->monto,
            'numerocuota' => (int) ($data['numerocuota'] ?? $this->numerocuota),
            'referencia' => $data['referencia'] ?? $this->referencia,
        ];

        return $this->update($payload);
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d');
    }
}
