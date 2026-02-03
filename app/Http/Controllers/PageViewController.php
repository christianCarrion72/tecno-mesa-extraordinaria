<?php

namespace App\Http\Controllers;

use App\Models\PageView;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class PageViewController extends Controller
{
    /**
     * Obtener el total de páginas únicas vistas por el usuario actual
     */
    public function uniquePagesCount()
    {
        $userId = Auth::id();
        
        return PageView::where('user_id', $userId)
            ->distinct('page_name')
            ->count('page_name');
    }

    /**
     * Obtener el total de vistas de página del usuario actual
     */
    public function totalViewsCount()
    {
        return Auth::user()->pageViews()->count();
    }

    /**
     * Obtener las páginas vistas por el usuario ordenadas por frecuencia
     */
    public function getMostViewedPages($limit = 5)
    {
        $userId = Auth::id();

        return PageView::where('user_id', $userId)
            ->select('page_name')
            ->selectRaw('COUNT(*) as view_count')
            ->groupBy('page_name')
            ->orderByDesc('view_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtener vistas de página de hoy
     */
    public function getTodayViews()
    {
        return Auth::user()->pageViews()
            ->whereDate('created_at', today())
            ->count();
    }

    /**
     * Obtener vistas de página de esta semana
     */
    public function getWeekViews()
    {
        return Auth::user()->pageViews()
            ->whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
            ])
            ->count();
    }

    /**
     * Obtener vistas de página de este mes
     */
    public function getMonthViews()
    {
        return Auth::user()->pageViews()
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
    }

    /**
     * Obtener todas las vistas con paginación
     */
    public function getAllViews($perPage = 15)
    {
        return Auth::user()->pageViews()
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Obtener vistas de una página específica
     */
    public function getPageViews($pageName)
    {
        $count = PageView::where('user_id', Auth::id())
            ->where('page_name', $pageName)
            ->count();
        
        return response()->json($count);
    }

    /**
     * Obtener estadísticas generales del usuario
     */
    public function getStats()
    {
        return [
            'total_unique_pages' => $this->uniquePagesCount(),
            'total_views' => $this->totalViewsCount(),
            'today_views' => $this->getTodayViews(),
            'week_views' => $this->getWeekViews(),
            'month_views' => $this->getMonthViews(),
            'most_viewed_pages' => $this->getMostViewedPages(10),
        ];
    }
}
