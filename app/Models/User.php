<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios'; // Especificar la tabla correcta

    protected $fillable = [
        'nombre',
        'email',
        'password_hash', // Usar password_hash en lugar de password
        'telefono',
        'direccion',
        'tipo',
        'estado',
        'foto',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Sobrescribir el método getAuthPassword para usar password_hash
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    // Mutator para cuando se asigne un valor a 'password'
    public function setPasswordAttribute($value)
    {
        $this->attributes['password_hash'] = \Illuminate\Support\Facades\Hash::make($value);
    }

    // Relaciones
    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class, 'cliente_id');
    }

    public function citas()
    {
        return $this->hasMany(Cita::class, 'cliente_id');
    }

    public function diagnosticos()
    {
        return $this->hasMany(Diagnostico::class, 'mecanico_id');
    }

    public function ordenesTrabajo()
    {
        return $this->hasMany(OrdenTrabajo::class, 'mecanico_id');
    }

    public function pagosRecibidos()
    {
        return $this->hasMany(PagoDetalle::class, 'recibido_por');
    }

    public function pageViews()
    {
        return $this->hasMany(PageView::class);
    }

    // Scopes
    public function scopeClientes($query)
    {
        return $query->where('tipo', 'cliente');
    }

    public function scopeMecanicos($query)
    {
        return $query->where('tipo', 'mecanico');
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    // Métodos de utilidad
    public function esCliente()
    {
        return $this->tipo === 'cliente';
    }

    public function esMecanico()
    {
        return $this->tipo === 'mecanico';
    }

    public function esSecretaria()
    {
        return $this->tipo === 'secretaria';
    }

    public function esPropietario()
    {
        return $this->tipo === 'propietario';
    }
}
