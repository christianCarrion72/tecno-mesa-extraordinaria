<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use SoftDeletes;

    protected $table = 'clientes';

    protected $fillable = [
        'nombre',
        'telefono',
        'foto',
    ];

    protected $dates = ['deleted_at'];

    /**
     * Relación con órdenes de trabajo
     */
    public function ordenesTrabajos()
    {
        return $this->hasMany(OrdenTrabajo::class, 'cliente_id');
    }

    /**
     * Verifica si el cliente tiene órdenes de trabajo activas
     */
    public function tieneOrdenesActivas()
    {
        return $this->ordenesTrabajos()
            ->whereIn('estado', ['pendiente', 'aprobado', 'proceso'])
            ->exists();
    }
}
