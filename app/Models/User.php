<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable, SoftDeletes;


    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'rol_id',
        'telefono',
        'direccion',
        'foto',
        //'tipo',
    ];

    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'two_factor_confirmed_at' => 'datetime',
    ];

    public function rol()
    {
        return $this->belongsTo(Rol::class);
    }

    public function tienePermiso(string $permiso): bool
    {
        return $this->rol->permisos->contains('nombre', $permiso);
    }

    public function obtenerPermisos(): array
    {
        return $this->rol->permisos->pluck('nombre')->toArray();
    }

    // Sobrescribir el método getAuthPassword para usar password_hash
    // public function getAuthPassword()
    // {
    //     return $this->password_hash;
    // }

    // // Mutator para cuando se asigne un valor a 'password'
    // public function setPasswordAttribute($value)
    // {
    //     $this->attributes['password_hash'] = \Illuminate\Support\Facades\Hash::make($value);
    // }

    // // Relaciones
    // public function vehiculos()
    // {
    //     return $this->hasMany(Vehiculo::class, 'cliente_id');
    // }

    // public function citas()
    // {
    //     return $this->hasMany(Cita::class, 'cliente_id');
    // }

    // public function diagnosticos()
    // {
    //     return $this->hasMany(Diagnostico::class, 'mecanico_id');
    // }

    // public function ordenesTrabajo()
    // {
    //     return $this->hasMany(OrdenTrabajo::class, 'mecanico_id');
    // }

    // public function pagosRecibidos()
    // {
    //     return $this->hasMany(PagoDetalle::class, 'recibido_por');
    // }

    public function pageViews()
    {
        return $this->hasMany(PageView::class);
    }

    // // Scopes
    // public function scopeClientes($query)
    // {
    //     return $query->where('tipo', 'cliente');
    // }

    // public function scopeMecanicos($query)
    // {
    //     return $query->where('tipo', 'mecanico');
    // }

    // public function scopeActivos($query)
    // {
    //     return $query->where('estado', 'activo');
    // }

    // // Métodos de utilidad
    // public function esCliente()
    // {
    //     return $this->tipo === 'cliente';
    // }

    // public function esMecanico()
    // {
    //     return $this->tipo === 'mecanico';
    // }

    // public function esSecretaria()
    // {
    //     return $this->tipo === 'secretaria';
    // }

    // public function esPropietario()
    // {
    //     return $this->tipo === 'propietario';
    // }
}
