<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Cloudinary\Cloudinary;
use Cloudinary\Api\Upload\UploadApi;

class ModeloController extends Controller
{
    public function index(Request $request)
    {
        if (!request()->user()->tienePermiso('modelo.listar')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para listar modelos.');
        }

        $modelos = Modelo::orderBy('id', 'desc')
            ->paginate(5)
            ->withQueryString();

        return inertia('Modelos/Index', [
            'modelos' => $modelos,
        ]);
    }

    public function create()
    {
        if (!request()->user()->tienePermiso('modelo.crear')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para crear modelos.');
        }

        return inertia('Modelos/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
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
                    'folder' => 'modelos',
                    'resource_type' => 'image'
                ]
            );
            
            $data['foto'] = $result['secure_url'];
        }

        Modelo::create($data);

        return redirect()->route('modelos.index')->with('success', 'Modelo creado correctamente.');
    }

    public function edit(Modelo $modelo)
    {
        if (!request()->user()->tienePermiso('modelo.editar')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para editar modelos.');
        }

        return inertia('Modelos/Edit', compact('modelo'));
    }

    public function update(Request $request, Modelo $modelo)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
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
                    'folder' => 'modelos',
                    'resource_type' => 'image'
                ]
            );
            
            $data['foto'] = $result['secure_url'];
        }

        $modelo->update($data);

        return redirect()->route('modelos.index')->with('success', 'Modelo actualizado correctamente.');
    }

    public function destroy(Modelo $modelo)
    {
        if (!request()->user()->tienePermiso('modelo.eliminar')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para eliminar modelos.');
        }

        try {
            // Verificar si tiene motores activos
            $tieneMotoresActivos = DB::table('motores')
                ->where('modelo_id', $modelo->id)
                ->whereNull('deleted_at')
                ->exists();

            if ($tieneMotoresActivos) {
                return redirect()->route('modelos.index')->with('error', 'No se puede eliminar este modelo porque tiene motores asociados. Por favor, elimine o reasigne los motores antes de continuar.');
            }

            // Si no tiene motores activos, hacer soft delete
            $modelo->delete();
            return redirect()->route('modelos.index')->with('success', 'Modelo eliminado correctamente.');
            
        } catch (\Illuminate\Database\QueryException $e) {
            // Capturar errores de restricción de base de datos
            if ($e->getCode() === '23503' || strpos($e->getMessage(), 'foreign key') !== false) {
                return redirect()->route('modelos.index')->with('error', 'No se puede eliminar este modelo porque tiene registros relacionados en el sistema. Por favor, elimine primero los registros dependientes.');
            }
            
            return redirect()->route('modelos.index')->with('error', 'Ocurrió un error al intentar eliminar el modelo. Por favor, intente nuevamente.');
            
        } catch (\Exception $e) {
            return redirect()->route('modelos.index')->with('error', 'Error inesperado. Por favor, contacte al administrador.');
        }
    }
}
