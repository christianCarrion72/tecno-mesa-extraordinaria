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
use App\Http\Controllers\Admin\UsuarioController as AdminUsuarioController;
use App\Http\Controllers\Admin\PermisoController as AdminPermisoController;


use App\Http\Controllers\Mecanico\DashboardController as MecanicoDashboardController;
use App\Http\Controllers\Mecanico\DiagnosticoController as MecanicoDiagnosticoController;
use App\Http\Controllers\Mecanico\OrdenController as MecanicoOrdenController;

use App\Http\Controllers\Cliente\DashboardController as ClienteDashboardController;
use App\Http\Controllers\Cliente\VehiculoController as ClienteVehiculoController;
use App\Http\Controllers\Cliente\CitaController as ClienteCitaController;
use App\Http\Controllers\Cliente\OrdenController as ClienteOrdenController;
use App\Http\Controllers\Cliente\PagoController as ClientePagoController;

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\ModeloController;
use App\Http\Controllers\MotorController;
use App\Http\Controllers\ParteController;
use App\Http\Controllers\OrdenTrabajoController;
use App\Http\Controllers\OrdenTrabajoServicioController;
use App\Http\Controllers\IncidenciaController;
use App\Http\Controllers\PlanPagoController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\PagoController;

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
Route::post('/pagofacil/callback', [PagoController::class, 'pagofacilCallback'])
    ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class)
    ->name('pagofacil.callback');
Route::post('/webhooks/pagofacil', [PagoController::class, 'pagofacilCallback'])
    ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class)
    ->name('pagofacil.callback.webhook');

Route::get('/pagos/tarjeta/{token}', [PagoController::class, 'stripeCheckoutPublic'])
    ->name('pagos.stripe.checkout');
Route::post('/pagos/tarjeta/{token}/confirm', [PagoController::class, 'stripeConfirmPublic'])
    ->name('pagos.stripe.confirm.public');

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

    // ============================================================================
    // GESTIÓN DE MARCAS, MODELOS, MOTORES, PARTES Y SERVICIOS
    // ============================================================================
    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
    Route::get('/clientes/create', [ClienteController::class, 'create'])->name('clientes.create');
    Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
    Route::get('/clientes/{cliente}/edit', [ClienteController::class, 'edit'])->name('clientes.edit');
    Route::put('/clientes/{cliente}', [ClienteController::class, 'update'])->name('clientes.update');
    Route::delete('/clientes/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy');

    Route::get('/marcas', [MarcaController::class, 'index'])->name('marcas.index');
    Route::get('/marcas/create', [MarcaController::class, 'create'])->name('marcas.create');
    Route::post('/marcas', [MarcaController::class, 'store'])->name('marcas.store');
    Route::get('/marcas/{marca}/edit', [MarcaController::class, 'edit'])->name('marcas.edit');
    Route::put('/marcas/{marca}', [MarcaController::class, 'update'])->name('marcas.update');
    Route::delete('/marcas/{marca}', [MarcaController::class, 'destroy'])->name('marcas.destroy');

    Route::get('/modelos', [ModeloController::class, 'index'])->name('modelos.index');
    Route::get('/modelos/create', [ModeloController::class, 'create'])->name('modelos.create');
    Route::post('/modelos', [ModeloController::class, 'store'])->name('modelos.store');
    Route::get('/modelos/{modelo}/edit', [ModeloController::class, 'edit'])->name('modelos.edit');
    Route::put('/modelos/{modelo}', [ModeloController::class, 'update'])->name('modelos.update');
    Route::delete('/modelos/{modelo}', [ModeloController::class, 'destroy'])->name('modelos.destroy');

    Route::get('/motores', [MotorController::class, 'index'])->name('motores.index');
    Route::get('/motores/create', [MotorController::class, 'create'])->name('motores.create');
    Route::post('/motores', [MotorController::class, 'store'])->name('motores.store');
    Route::get('/motores/{motor}/edit', [MotorController::class, 'edit'])->name('motores.edit');
    Route::put('/motores/{motor}', [MotorController::class, 'update'])->name('motores.update');
    Route::delete('/motores/{motor}', [MotorController::class, 'destroy'])->name('motores.destroy');

    Route::get('/partes', [ParteController::class, 'index'])->name('partes.index');
    Route::get('/partes/create', [ParteController::class, 'create'])->name('partes.create');
    Route::post('/partes', [ParteController::class, 'store'])->name('partes.store');
    Route::get('/partes/{parte}/edit', [ParteController::class, 'edit'])->name('partes.edit');
    Route::put('/partes/{parte}', [ParteController::class, 'update'])->name('partes.update');
    Route::delete('/partes/{parte}', [ParteController::class, 'destroy'])->name('partes.destroy');

    Route::get('/servicios', [\App\Http\Controllers\ServicioController::class, 'index'])->name('servicios.index');
    Route::get('/servicios/create', [\App\Http\Controllers\ServicioController::class, 'create'])->name('servicios.create');
    Route::post('/servicios', [\App\Http\Controllers\ServicioController::class, 'store'])->name('servicios.store');
    Route::get('/servicios/{servicio}/edit', [\App\Http\Controllers\ServicioController::class, 'edit'])->name('servicios.edit');
    Route::put('/servicios/{servicio}', [\App\Http\Controllers\ServicioController::class, 'update'])->name('servicios.update');
    Route::delete('/servicios/{servicio}', [\App\Http\Controllers\ServicioController::class, 'destroy'])->name('servicios.destroy');

    Route::get('/orden-trabajos', [OrdenTrabajoController::class, 'index'])->name('orden-trabajos.index');
    Route::get('/orden-trabajos/create', [OrdenTrabajoController::class, 'create'])->name('orden-trabajos.create');
    Route::post('/orden-trabajos', [OrdenTrabajoController::class, 'store'])->name('orden-trabajos.store');
    Route::get('/orden-trabajos/{ordenTrabajo}', [OrdenTrabajoController::class, 'show'])->name('orden-trabajos.show');
    Route::get('/orden-trabajos/{ordenTrabajo}/edit', [OrdenTrabajoController::class, 'edit'])->name('orden-trabajos.edit');
    Route::put('/orden-trabajos/{ordenTrabajo}', [OrdenTrabajoController::class, 'update'])->name('orden-trabajos.update');
    Route::put('/orden-trabajos/{ordenTrabajo}/estado', [OrdenTrabajoController::class, 'actualizarEstado'])->name('orden-trabajos.actualizar-estado');
    Route::delete('/orden-trabajos/{ordenTrabajo}', [OrdenTrabajoController::class, 'destroy'])->name('orden-trabajos.destroy');

    Route::post('/orden-trabajos/{ordenTrabajo}/servicios', [OrdenTrabajoServicioController::class, 'store'])->name('orden-trabajos.servicios.store');
    Route::put('/orden-trabajos/servicios/{detalle}', [OrdenTrabajoServicioController::class, 'update'])->name('orden-trabajos.servicios.update');
    Route::delete('/orden-trabajos/servicios/{detalle}', [OrdenTrabajoServicioController::class, 'destroy'])->name('orden-trabajos.servicios.destroy');

    Route::get('/incidencias', [IncidenciaController::class, 'index'])->name('incidencias.index');
    Route::get('/orden-trabajos/{ordenTrabajo}/incidencias/create', [IncidenciaController::class, 'create'])->name('incidencias.create');
    Route::post('/orden-trabajos/{ordenTrabajo}/incidencias', [IncidenciaController::class, 'store'])->name('incidencias.store');
    Route::get('/incidencias/{incidencia}/edit', [IncidenciaController::class, 'edit'])->name('incidencias.edit');
    Route::put('/incidencias/{incidencia}', [IncidenciaController::class, 'update'])->name('incidencias.update');
    Route::delete('/incidencias/{incidencia}', [IncidenciaController::class, 'destroy'])->name('incidencias.destroy');

    Route::get('/plan-pagos', [PlanPagoController::class, 'index'])->name('plan-pagos.index');
    Route::get('/plan-pagos/{planPago}', [PlanPagoController::class, 'show'])->name('plan-pagos.show');
    Route::get('/orden-trabajos/{ordenTrabajo}/plan-pagos/create', [PlanPagoController::class, 'create'])->name('plan-pagos.create');
    Route::post('/orden-trabajos/{ordenTrabajo}/plan-pagos', [PlanPagoController::class, 'store'])->name('plan-pagos.store');
    Route::get('/plan-pagos/{planPago}/edit', [PlanPagoController::class, 'edit'])->name('plan-pagos.edit');
    Route::put('/plan-pagos/{planPago}', [PlanPagoController::class, 'update'])->name('plan-pagos.update');
    Route::delete('/plan-pagos/{planPago}', [PlanPagoController::class, 'destroy'])->name('plan-pagos.destroy');

    Route::get('/plan-pagos/{planPago}/pagos', [PagoController::class, 'index'])->name('plan-pagos.pagos.index');
    Route::get('/plan-pagos/{planPago}/pagos/create', [PagoController::class, 'create'])->name('plan-pagos.pagos.create');
    Route::post('/plan-pagos/{planPago}/pagos', [PagoController::class, 'store'])->name('plan-pagos.pagos.store');
    Route::get('/pagos/{pago}', [PagoController::class, 'show'])->name('plan-pagos.pagos.show');
    Route::get('/pagos/{pago}/edit', [PagoController::class, 'edit'])->name('plan-pagos.pagos.edit');
    Route::put('/pagos/{pago}', [PagoController::class, 'update'])->name('plan-pagos.pagos.update');
    Route::delete('/pagos/{pago}', [PagoController::class, 'destroy'])->name('plan-pagos.pagos.destroy');

    Route::get('/stripe/config', [PagoController::class, 'stripeConfig'])->name('stripe.config');
    Route::post('/plan-pagos/{planPago}/pagos/stripe/intent', [PagoController::class, 'stripeCreatePaymentIntent'])->name('plan-pagos.pagos.stripe.intent');
    Route::post('/plan-pagos/{planPago}/pagos/stripe/confirm', [PagoController::class, 'stripeConfirmPago'])->name('plan-pagos.pagos.stripe.confirm');

    Route::get('/pagofacil/login', [PagoController::class, 'pagofacilLogin'])->name('pagofacil.login');
    Route::get('/pagofacil/list-enabled-services', [PagoController::class, 'pagofacilListEnabledServices'])->name('pagofacil.list');
    Route::post('/pagofacil/generate-qr', [PagoController::class, 'pagofacilGenerateQr'])
        ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class)
        ->name('pagofacil.generate');
    Route::get('/pagofacil/callback-url', [PagoController::class, 'pagofacilCallbackUrl'])->name('pagofacil.callback-url');
    Route::post('/pagofacil/query-transaction', [PagoController::class, 'pagofacilQueryTransaction'])->name('pagofacil.query');

    Route::get('/facturas/{factura}', [FacturaController::class, 'show'])->name('facturas.show');
    Route::get('/pagos/{pago}/facturas/create', [FacturaController::class, 'create'])->name('facturas.create');
    Route::post('/pagos/{pago}/facturas', [FacturaController::class, 'store'])->name('facturas.store');
    Route::delete('/facturas/{factura}', [FacturaController::class, 'destroy'])->name('facturas.destroy');

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
        Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
        Route::get('/clientes/create', [ClienteController::class, 'create'])->name('clientes.create');
        Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
        Route::get('/clientes/{cliente}/edit', [ClienteController::class, 'edit'])->name('clientes.edit');
        Route::put('/clientes/{cliente}', [ClienteController::class, 'update'])->name('clientes.update');
        Route::delete('/clientes/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy');

        // Gestión de Vehículos
        Route::get('/vehiculos', [AdminVehiculoController::class, 'index'])->name('vehiculos.index');
        Route::get('/vehiculos/create', [AdminVehiculoController::class, 'create'])->name('vehiculos.create');
        Route::post('/vehiculos', [AdminVehiculoController::class, 'store'])->name('vehiculos.store');
        Route::get('/vehiculos/{vehiculo}', [AdminVehiculoController::class, 'show'])->name('vehiculos.show');
        Route::get('/vehiculos/{vehiculo}/edit', [AdminVehiculoController::class, 'edit'])->name('vehiculos.edit');
        Route::put('/vehiculos/{vehiculo}', [AdminVehiculoController::class, 'update'])->name('vehiculos.update');
        Route::delete('/vehiculos/{vehiculo}', [AdminVehiculoController::class, 'destroy'])->name('vehiculos.destroy');

        // Gestión de Usuarios
        Route::get('/usuarios', [AdminUsuarioController::class, 'index'])->name('usuarios.index');
        Route::get('/usuarios/create', [AdminUsuarioController::class, 'create'])->name('usuarios.create');
        Route::post('/usuarios', [AdminUsuarioController::class, 'store'])->name('usuarios.store');
        Route::get('/usuarios/{usuario}/edit', [AdminUsuarioController::class, 'edit'])->name('usuarios.edit');
        Route::put('/usuarios/{usuario}', [AdminUsuarioController::class, 'update'])->name('usuarios.update');
        Route::delete('/usuarios/{usuario}', [AdminUsuarioController::class, 'destroy'])->name('usuarios.destroy');

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
        // Exportación puede usarse via GET (descarga directa) o POST (form Inertia)
        Route::get('/reportes/exportar', [AdminReportController::class, 'exportar'])->name('reportes.exportar');
        Route::post('/reportes/exportar', [AdminReportController::class, 'exportar']);

        // Gestión de Permisos
        Route::get('/permisos', [AdminPermisoController::class, 'index'])->name('permisos.index');
        Route::post('/permisos/actualizar', [AdminPermisoController::class, 'actualizar'])->name('permisos.actualizar');

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
