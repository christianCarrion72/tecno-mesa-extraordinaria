<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Vehiculo extends Model
{
    use HasFactory;

    protected $table = 'vehiculos';

    protected $fillable = [
        'cliente_id',
        'placa',
        'marca',
        'modelo',
        'anio',
        'color',
        'kilometraje',
        'foto',
        'observaciones',
        'estado',
    ];

    protected $casts = [
        'anio' => 'integer',
        'kilometraje' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // =========================================================================
    // RELACIONES
    // =========================================================================

    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function citas()
    {
        return $this->hasMany(Cita::class);
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopeDelCliente($query, $clienteId)
    {
        return $query->where('cliente_id', $clienteId);
    }

    public function scopeConPlaca($query, $placa)
    {
        return $query->where('placa', 'LIKE', "%{$placa}%");
    }

    // =========================================================================
    // MÉTODOS DE UTILIDAD
    // =========================================================================

    public function getFotoUrlAttribute()
    {
        if (!$this->foto) {
            return null;
        }

        // Verifica que el archivo existe
        if (Storage::disk('public')->exists($this->foto)) {
            // Usar asset() en lugar de Storage::url()
            return asset('storage/' . $this->foto);
        }

        return null;
    }

    /**
     * Verificar si el vehículo tiene foto
     */
    public function tieneFoto()
    {
        return !empty($this->foto) && Storage::disk('public')->exists($this->foto);
    }

    /**
     * Eliminar la foto del vehículo
     */
    public function eliminarFoto()
    {
        if ($this->tieneFoto()) {
            Storage::delete($this->foto);
            $this->update(['foto' => null]);
        }
    }

    /**
     * Obtener información resumida del vehículo
     */
    public function getInfoResumidaAttribute()
    {
        return [
            'id' => $this->id,
            'placa' => $this->placa,
            'marca' => $this->marca,
            'modelo' => $this->modelo,
            'anio' => $this->anio,
            'color' => $this->color,
            'cliente' => $this->cliente->nombre,
            'foto_url' => $this->foto_url,
        ];
    }

    /**
     * Obtener el nombre completo del vehículo
     */
    public function getNombreCompletoAttribute()
    {
        return "{$this->marca} {$this->modelo} {$this->anio}";
    }

    /**
     * Obtener estadísticas del vehículo
     */
    public function getEstadisticasAttribute()
    {
        return [
            'total_citas' => $this->citas()->count(),
            'citas_pendientes' => $this->citas()->where('estado', 'pendiente')->count(),
            'ultima_cita' => $this->citas()->latest()->first()?->created_at->format('d/m/Y'),
        ];
    }
}
