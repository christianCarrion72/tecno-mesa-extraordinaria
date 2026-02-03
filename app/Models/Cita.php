<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    use HasFactory;
    protected $table = 'citas';

    protected $fillable = [
        'codigo',
        'cliente_id',
        'vehiculo_id',
        'fecha',
        'hora',
        'motivo',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora' => 'string',
    ];

    // Relaciones
    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function diagnostico()
    {
        return $this->hasOne(Diagnostico::class);
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeConfirmadas($query)
    {
        return $this->where('estado', 'confirmada');
    }

    public function scopeDelCliente($query, $clienteId)
    {
        return $query->where('cliente_id', $clienteId);
    }
}
