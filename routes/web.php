<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\CitaController as AdminCitaController;
use App\Http\Controllers\Admin\ClienteController as AdminClienteController;
use App\Http\Controllers\Admin\VehiculoController as AdminVehiculoController;
use App\Http\Controllers\Admin\ServicioController as AdminServicioController;
use App\Http\Controllers\Admin\OrdenController as AdminOrdenController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\ConfiguracionController as AdminConfiguracionController;
use App\Http\Controllers\Admin\DiagnosticoController as AdminDiagnosticoController;
use App\Http\Controllers\Admin\PagoController as AdminPagoController;
use App\Http\Controllers\Admin\PagoFacilController as AdminPagoFacilController;


use App\Http\Controllers\Mecanico\DashboardController as MecanicoDashboardController;
use App\Http\Controllers\Mecanico\DiagnosticoController as MecanicoDiagnosticoController;
use App\Http\Controllers\Mecanico\OrdenController as MecanicoOrdenController;

use App\Http\Controllers\Cliente\DashboardController as ClienteDashboardController;
use App\Http\Controllers\Cliente\VehiculoController as ClienteVehiculoController;
use App\Http\Controllers\Cliente\CitaController as ClienteCitaController;
use App\Http\Controllers\Cliente\OrdenController as ClienteOrdenController;
use App\Http\Controllers\Cliente\PagoController as ClientePagoController;

use App\Http\Controllers\Api\QrController as QrController;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\PageViewController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;

// ============================================================================
// RUTAS PÚBLICAS
// ============================================================================
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

// ⚠️ IMPORTANTE: Rutas del callback y PagoFácil (return se mantiene aqui, callback en api.php)
Route::get('/pagos/pagofacil/return', [AdminPagoFacilController::class, 'return'])->name('pagos.return');

// ============================================================================
// RUTAS DE AUTENTICACIÓN
// ============================================================================
require __DIR__ . '/auth.php';

// ============================================================================
// RUTAS PROTEGIDAS POR AUTENTICACIÓN
// ============================================================================
Route::middleware(['auth', 'verified'])->group(function () {

    // ========================================================================
    // DASHBOARDS POR TIPO DE USUARIO
    // ========================================================================

    // Dashboard para Clientes (ruta por defecto)
    Route::get('/dashboard', [ClienteDashboardController::class, 'index'])->name('dashboard');

    // ========================================================================
    // RUTAS DE PERFIL (ACCESIBLE PARA TODOS LOS TIPOS)
    // ========================================================================
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ========================================================================
    // RUTAS ESPECÍFICAS PARA CLIENTES
    // ========================================================================
    Route::middleware('tipo:cliente')->prefix('cliente')->name('cliente.')->group(function () {
        // Dashboard Cliente
        Route::get('/dashboard', [ClienteDashboardController::class, 'index'])->name('dashboard');

        // Vehículos
        Route::get('/vehiculos', [ClienteVehiculoController::class, 'index'])->name('vehiculos.index');
        Route::get('/vehiculos/create', [ClienteVehiculoController::class, 'create'])->name('vehiculos.create');
        Route::post('/vehiculos', [ClienteVehiculoController::class, 'store'])->name('vehiculos.store');
        Route::get('/vehiculos/{vehiculo}', [ClienteVehiculoController::class, 'show'])->name('vehiculos.show');
        Route::get('/vehiculos/{vehiculo}/edit', [ClienteVehiculoController::class, 'edit'])->name('vehiculos.edit');
        Route::put('/vehiculos/{vehiculo}', [ClienteVehiculoController::class, 'update'])->name('vehiculos.update');
        Route::delete('/vehiculos/{vehiculo}', [ClienteVehiculoController::class, 'destroy'])->name('vehiculos.destroy');

        // Citas
        Route::get('/citas', [ClienteCitaController::class, 'index'])->name('citas.index');
        Route::get('/citas/create', [ClienteCitaController::class, 'create'])->name('citas.create');
        Route::post('/citas', [ClienteCitaController::class, 'store'])->name('citas.store');
        Route::get('/citas/{cita}', [ClienteCitaController::class, 'show'])->name('citas.show');
        Route::get('/citas/{cita}/edit', [ClienteCitaController::class, 'edit'])->name('citas.edit');
        Route::put('/citas/{cita}', [ClienteCitaController::class, 'update'])->name('citas.update');
        Route::delete('/citas/{cita}', [ClienteCitaController::class, 'destroy'])->name('citas.destroy');

        // Órdenes de Trabajo - Cliente
        Route::get('/ordenes', [ClienteOrdenController::class, 'index'])->name('ordenes.index');
        Route::get('/ordenes/{orden}', [ClienteOrdenController::class, 'show'])->name('ordenes.show');
        Route::post('/ordenes/{orden}/aprobar', [ClienteOrdenController::class, 'aprobar'])->name('ordenes.aprobar');
        Route::post('/ordenes/{orden}/rechazar', [ClienteOrdenController::class, 'rechazar'])->name('ordenes.rechazar');
        Route::get('/ordenes/{orden}/descargar-presupuesto', [ClienteOrdenController::class, 'descargarPresupuesto'])->name('ordenes.descargar-presupuesto');

        // Pagos - Cliente
        Route::get('/pagos', [ClientePagoController::class, 'index'])->name('pagos.index');
        Route::get('/pagos/{pago}', [ClientePagoController::class, 'show'])->name('pagos.show');
        Route::get('/pagos/{pago}/pagar', [ClientePagoController::class, 'pagar'])->name('pagos.pagar');
        Route::post('/pagos/{pago}/procesar-qr', [ClientePagoController::class, 'procesarQr'])->name('pagos.procesar-qr');
        Route::post('/pagos/{pago}/confirmar-efectivo', [ClientePagoController::class, 'confirmarEfectivo'])->name('pagos.confirmar-efectivo');

        // PagoFácil - Cliente puede generar QR para sus pagos
        Route::post('/pagofacil/generar-qr', [AdminPagoFacilController::class, 'generarQR'])->name('pagofacil.generar-qr');
        Route::post('/pagofacil/consultar-estado', [AdminPagoFacilController::class, 'consultarEstado'])->name('pagofacil.consultar-estado');

    });

    // ========================================================================
    // RUTAS ESPECÍFICAS PARA MECÁNICOS
    // ========================================================================
    Route::middleware('tipo:mecanico')->prefix('mecanico')->name('mecanico.')->group(function () {
        // Dashboard Mecánico
        Route::get('/dashboard', [MecanicoDashboardController::class, 'index'])->name('dashboard');

        // Diagnosticos
        Route::get('/diagnosticos', [MecanicoDiagnosticoController::class, 'index'])->name('diagnosticos.index');
        Route::get('/diagnosticos/{diagnostico}', [MecanicoDiagnosticoController::class, 'show'])->name('diagnosticos.show');
        Route::get('/diagnosticos/{diagnostico}/edit', [MecanicoDiagnosticoController::class, 'edit'])->name('diagnosticos.edit');
        Route::put('/diagnosticos/{diagnostico}', [MecanicoDiagnosticoController::class, 'update'])->name('diagnosticos.update');

        // Órdenes de Trabajo
        Route::get('/ordenes', [MecanicoOrdenController::class, 'index'])->name('ordenes.index');
        Route::get('/ordenes/{orden}', [MecanicoOrdenController::class, 'show'])->name('ordenes.show');
        Route::get('/ordenes/{orden}/edit', [MecanicoOrdenController::class, 'edit'])->name('ordenes.edit');
        Route::put('/ordenes/{orden}', [MecanicoOrdenController::class, 'update'])->name('ordenes.update');
    });

    // ========================================================================
    // RUTAS ESPECÍFICAS PARA ADMINISTRADORES (PROPIETARIO/SECRETARIA)
    // ========================================================================
    Route::middleware('tipo:propietario,secretaria')->prefix('admin')->name('admin.')->group(function () {
        // Dashboard Admin
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Gestión de Citas
        Route::get('/citas', [AdminCitaController::class, 'index'])->name('citas.index');
        Route::get('/citas/create', [AdminCitaController::class, 'create'])->name('citas.create');
        Route::post('/citas', [AdminCitaController::class, 'store'])->name('citas.store');
        Route::get('/citas/{cita}', [AdminCitaController::class, 'show'])->name('citas.show');
        Route::get('/citas/{cita}/edit', [AdminCitaController::class, 'edit'])->name('citas.edit');
        Route::put('/citas/{cita}', [AdminCitaController::class, 'update'])->name('citas.update');
        Route::delete('/citas/{cita}', [AdminCitaController::class, 'destroy'])->name('citas.destroy');
        Route::patch('/citas/{cita}/status', [AdminCitaController::class, 'updateStatus'])->name('citas.update-status');
        Route::get('/citas/por-fecha', [AdminCitaController::class, 'getCitasPorFecha'])->name('citas.por-fecha');

        // Gestión de Clientes
        Route::get('/clientes', [AdminClienteController::class, 'index'])->name('clientes.index');
        Route::get('/clientes/create', [AdminClienteController::class, 'create'])->name('clientes.create');
        Route::post('/clientes', [AdminClienteController::class, 'store'])->name('clientes.store');
        Route::get('/clientes/{cliente}', [AdminClienteController::class, 'show'])->name('clientes.show');
        Route::get('/clientes/{cliente}/edit', [AdminClienteController::class, 'edit'])->name('clientes.edit');
        Route::put('/clientes/{cliente}', [AdminClienteController::class, 'update'])->name('clientes.update');
        Route::delete('/clientes/{cliente}', [AdminClienteController::class, 'destroy'])->name('clientes.destroy');

        // Gestión de Vehículos
        Route::get('/vehiculos', [AdminVehiculoController::class, 'index'])->name('vehiculos.index');
        Route::get('/vehiculos/create', [AdminVehiculoController::class, 'create'])->name('vehiculos.create');
        Route::post('/vehiculos', [AdminVehiculoController::class, 'store'])->name('vehiculos.store');
        Route::get('/vehiculos/{vehiculo}', [AdminVehiculoController::class, 'show'])->name('vehiculos.show');
        Route::get('/vehiculos/{vehiculo}/edit', [AdminVehiculoController::class, 'edit'])->name('vehiculos.edit');
        Route::put('/vehiculos/{vehiculo}', [AdminVehiculoController::class, 'update'])->name('vehiculos.update');
        Route::delete('/vehiculos/{vehiculo}', [AdminVehiculoController::class, 'destroy'])->name('vehiculos.destroy');

        // Gestión de Servicios
        Route::get('/servicios', [AdminServicioController::class, 'index'])->name('servicios.index');
        Route::get('/servicios/create', [AdminServicioController::class, 'create'])->name('servicios.create');
        Route::post('/servicios', [AdminServicioController::class, 'store'])->name('servicios.store');
        Route::get('/servicios/{servicio}/edit', [AdminServicioController::class, 'edit'])->name('servicios.edit');
        Route::put('/servicios/{servicio}', [AdminServicioController::class, 'update'])->name('servicios.update');
        Route::delete('/servicios/{servicio}', [AdminServicioController::class, 'destroy'])->name('servicios.destroy');
        Route::put('/servicios/{servicio}/status', [AdminServicioController::class, 'updateStatus'])->name('servicios.status');

        // Gestión de Diagnósticos
        Route::get('/diagnosticos', [AdminDiagnosticoController::class, 'index'])->name('diagnosticos.index');
        Route::get('/diagnosticos/create', [AdminDiagnosticoController::class, 'create'])->name('diagnosticos.create');
        Route::post('/diagnosticos', [AdminDiagnosticoController::class, 'store'])->name('diagnosticos.store');
        Route::get('/diagnosticos/{diagnostico}', [AdminDiagnosticoController::class, 'show'])->name('diagnosticos.show');
        Route::get('/diagnosticos/{diagnostico}/edit', [AdminDiagnosticoController::class, 'edit'])->name('diagnosticos.edit');
        Route::put('/diagnosticos/{diagnostico}', [AdminDiagnosticoController::class, 'update'])->name('diagnosticos.update');
        Route::delete('/diagnosticos/{diagnostico}', [AdminDiagnosticoController::class, 'destroy'])->name('diagnosticos.destroy');
        Route::patch('/diagnosticos/{diagnostico}/status', [AdminDiagnosticoController::class, 'updateStatus'])->name('diagnosticos.update-status');

        // Gestión de Órdenes
        Route::get('/ordenes', [AdminOrdenController::class, 'index'])->name('ordenes.index');
        Route::get('/ordenes/create', [AdminOrdenController::class, 'create'])->name('ordenes.create');
        Route::post('/ordenes', [AdminOrdenController::class, 'store'])->name('ordenes.store');
        Route::get('/ordenes/{orden}', [AdminOrdenController::class, 'show'])->name('ordenes.show');
        Route::get('/ordenes/{orden}/edit', [AdminOrdenController::class, 'edit'])->name('ordenes.edit');
        Route::post('/ordenes/{orden}/add-service', [AdminOrdenController::class, 'addService'])->name('ordenes.add-service');
        Route::put('/ordenes/{orden}', [AdminOrdenController::class, 'update'])->name('ordenes.update');
        Route::delete('/ordenes/{orden}', [AdminOrdenController::class, 'removeService'])->name('ordenes.remove-service');

        // Gestión de Pagos
        Route::get('/pagos', [AdminPagoController::class, 'index'])->name('pagos.index');
        Route::get('/pagos/create', [AdminPagoController::class, 'create'])->name('pagos.create');
        Route::post('/pagos', [AdminPagoController::class, 'store'])->name('pagos.store');

        // RUTAS ESPECÍFICAS PRIMERO
        Route::get('/pagos/vencidos', [AdminPagoController::class, 'vencidos'])->name('pagos.vencidos');
        Route::get('/pagos/reporte', [AdminPagoController::class, 'reporte'])->name('pagos.reporte');

        // RUTAS CON PARÁMETROS DESPUÉS
        Route::get('/pagos/{pago}', [AdminPagoController::class, 'show'])->name('pagos.show');
        Route::get('/pagos/{pago}/edit', [AdminPagoController::class, 'edit'])->name('pagos.edit');
        Route::put('/pagos/{pago}', [AdminPagoController::class, 'update'])->name('pagos.update');
        Route::post('/pagos/{pago}/registrar', [AdminPagoController::class, 'registrarPago'])->name('pagos.registrar');
        Route::get('/pagos/{pago}/cobrar', [AdminPagoController::class, 'cobrar'])->name('pagos.cobrar');

        // === RUTAS DE PAGOFÁCIL ===
        Route::prefix('pagofacil')->name('pagofacil.')->group(function () {
            Route::get('/generar-qr', [AdminPagoFacilController::class, 'index'])->name('index');
            Route::post('/generar-qr', [AdminPagoFacilController::class, 'generarQR'])->name('generar-qr');
            Route::post('/consultar-estado', [AdminPagoFacilController::class, 'consultarEstado'])->name('consultar-estado');
            Route::get('/estado/{pago}', [AdminPagoFacilController::class, 'obtenerEstadoPago'])->name('obtener-estado');
        });

        // Generar QR
        Route::post('/pagos/{pago}/generar-qr', [QrController::class, 'generarQR'])->name('api.generar-qr');
        //Route::post('/pagos/callback', [QrController::class, 'handleCallback'])->name('api.pagos.callback');
        // Reportes
        Route::get('/reportes', [AdminReportController::class, 'index'])->name('reportes.index');
        Route::post('/reportes/exportar', [AdminReportController::class, 'exportar'])->name('reportes.exportar');

        // Configuración
        Route::get('/configuracion', [AdminConfiguracionController::class, 'index'])->name('configuracion.index');

    });

    // ========================================================================
    // RUTAS DE ESTADÍSTICAS DE PÁGINAS VISTAS (ACCESIBLE PARA TODOS)
    // ========================================================================
    Route::prefix('api/page-views')->name('api.page-views.')->group(function () {
        Route::get('/stats', [PageViewController::class, 'getStats'])->name('stats');
        Route::get('/unique-pages', [PageViewController::class, 'uniquePagesCount'])->name('unique-pages');
        Route::get('/total-views', [PageViewController::class, 'totalViewsCount'])->name('total-views');
        Route::get('/most-viewed', [PageViewController::class, 'getMostViewedPages'])->name('most-viewed');
        Route::get('/today', [PageViewController::class, 'getTodayViews'])->name('today');
        Route::get('/week', [PageViewController::class, 'getWeekViews'])->name('week');
        Route::get('/month', [PageViewController::class, 'getMonthViews'])->name('month');
        Route::get('/all', [PageViewController::class, 'getAllViews'])->name('all');
        Route::get('/page/{pageName}', [PageViewController::class, 'getPageViews'])->name('page');
    });
});

