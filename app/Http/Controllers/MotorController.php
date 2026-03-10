<?php

namespace App\Http\Controllers;

use App\Models\Motor;
use App\Models\Marca;
use App\Models\Modelo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Cloudinary\Cloudinary;
use Cloudinary\Api\Upload\UploadApi;

class MotorController extends Controller
{
    public function index(Request $request)
    {
        if (!request()->user()->tienePermiso('motor.listar')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para listar motores.');
        }

        return inertia('Motores/Index', [
            'motores' => Motor::with(['marca', 'modelo'])
            ->when(
                $request->busqueda,
                function ($query) use ($request) {
                    $query->whereRaw('LOWER(numero_serie) like LOWER(?)', ['%' . $request->busqueda . '%'])
                          ->orWhereRaw('LOWER(descripcion) like LOWER(?)', ['%' . $request->busqueda . '%'])
                          ->orWhereHas('marca', function($q) use ($request) {
                              $q->whereRaw('LOWER(nombre) like LOWER(?)', ['%' . $request->busqueda . '%']);
                          })
                          ->orWhereHas('modelo', function($q) use ($request) {
                              $q->whereRaw('LOWER(nombre) like LOWER(?)', ['%' . $request->busqueda . '%']);
                          });
                }
            )
            ->orderBy('id', 'desc')
            ->paginate(5)
            ->withQueryString(),

            'terminosBusqueda' => $request->busqueda
        ]);
    }

    public function create()
    {
        if (!request()->user()->tienePermiso('motor.crear')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para crear motores.');
        }

        $marcas = Marca::orderBy('nombre')->get();
        $modelos = Modelo::orderBy('nombre')->get();

        return inertia('Motores/Create', compact('marcas', 'modelos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'numero_serie' => 'required|string|max:255|unique:motores,numero_serie',
            'anio' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'descripcion' => 'nullable|string|max:500',
            'marca_id' => 'required|exists:marcas,id',
            'modelo_id' => 'required|exists:modelos,id',
            'foto' => 'nullable|image|max:2048',
        ]);

        $data = $request->except('foto');
        
        if ($request->hasFile('foto')) {
            $cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => config('cloudinary.cloud_name'),
                    'api_key' => config('cloudinary.api_key'),
                    'api_secret' => config('cloudinary.api_secret'),
                ],
            ]);

            $uploadApi = new UploadApi();
            $result = $uploadApi->upload(
                $request->file('foto')->getRealPath(),
                [
                    'folder' => 'motores',
                    'resource_type' => 'image'
                ]
            );
            
            $data['foto'] = $result['secure_url'];
        }

        Motor::create($data);

        return redirect()->route('motores.index')->with('success', 'Motor creado correctamente.');
    }

    public function edit(Motor $motor)
    {
        if (!request()->user()->tienePermiso('motor.editar')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para editar motores.');
        }

        $motor->load(['marca', 'modelo']);
        $marcas = Marca::orderBy('nombre')->get();
        $modelos = Modelo::orderBy('nombre')->get();
    
        return inertia('Motores/Edit', compact('motor', 'marcas', 'modelos'));
    }

    public function update(Request $request, Motor $motor)
    {
        $request->validate([
            'numero_serie' => 'required|string|max:255|unique:motores,numero_serie,' . $motor->id,
            'anio' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'descripcion' => 'nullable|string|max:500',
            'marca_id' => 'required|exists:marcas,id',
            'modelo_id' => 'required|exists:modelos,id',
            'foto' => 'nullable|image|max:2048',
        ]);

        $data = $request->except('foto');
        
        if ($request->hasFile('foto')) {
            $cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => config('cloudinary.cloud_name'),
                    'api_key' => config('cloudinary.api_key'),
                    'api_secret' => config('cloudinary.api_secret'),
                ],
            ]);

            $uploadApi = new UploadApi();
            $result = $uploadApi->upload(
                $request->file('foto')->getRealPath(),
                [
                    'folder' => 'motores',
                    'resource_type' => 'image'
                ]
            );
            
            $data['foto'] = $result['secure_url'];
        }

        $motor->update($data);

        return redirect()->route('motores.index')->with('success', 'Motor actualizado correctamente.');
    }

    public function destroy(Motor $motor)
    {
        try {
            // Verificar si tiene órdenes de trabajo activas
            $tieneOrdenesActivas = DB::table('orden_trabajos')
                ->where('motor_id', $motor->id)
                ->whereNull('deleted_at')
                ->exists();

            if ($tieneOrdenesActivas) {
                return redirect()->route('motores.index')->with('error', 'No se puede eliminar este motor porque está asignado a órdenes de trabajo activas. Por favor, reasigne o finalice las órdenes de trabajo antes de continuar.');
            }

            // Verificar si tiene partes activas
            $tienePartesActivas = DB::table('partes')
                ->where('motor_id', $motor->id)
                ->whereNull('deleted_at')
                ->exists();

            if ($tienePartesActivas) {
                return redirect()->route('motores.index')->with('error', 'No se puede eliminar este motor porque tiene partes asociadas. Por favor, elimine o reasigne las partes antes de continuar.');
            }

            // Si no tiene restricciones, hacer soft delete
            $motor->delete();
            return redirect()->route('motores.index')->with('success', 'Motor eliminado correctamente.');
            
        } catch (\Illuminate\Database\QueryException $e) {
            // Capturar errores de restricción de base de datos
            if ($e->getCode() === '23503' || strpos($e->getMessage(), 'foreign key') !== false) {
                return redirect()->route('motores.index')->with('error', 'No se puede eliminar este motor porque tiene registros relacionados en el sistema. Por favor, elimine primero los registros dependientes.');
            }
            
            return redirect()->route('motores.index')->with('error', 'Ocurrió un error al intentar eliminar el motor. Por favor, intente nuevamente.');
            
        } catch (\Exception $e) {
            return redirect()->route('motores.index')->with('error', 'Error inesperado. Por favor, contacte al administrador.');
        }
    }
}
