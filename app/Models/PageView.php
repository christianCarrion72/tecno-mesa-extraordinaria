<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageView extends Model
{
    use HasFactory;

    protected $table = 'page_views';

    protected $fillable = [
        'user_id',
        'page_name',
        'page_route',
        'ip_address',
        'user_agent',
        'referer',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relación con User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope para obtener vistas únicas por página
    public function scopeUniquePage($query, $userId)
    {
        return $query->where('user_id', $userId)
            ->distinct('page_name')
            ->select('page_name')
            ->get()
            ->count();
    }

    // Scope para obtener total de vistas de una página
    public function scopeByPage($query, $pageName)
    {
        return $query->where('page_name', $pageName);
    }

    // Scope para obtener vistas por usuario
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}

