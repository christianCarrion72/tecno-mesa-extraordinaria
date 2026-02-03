<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo',
        'precio_base',
        'duracion_estimada',
        'estado',
    ];

    // Relaciones
    public function ordenServicios()
    {
        return $this->hasMany(OrdenServicio::class);
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }
}