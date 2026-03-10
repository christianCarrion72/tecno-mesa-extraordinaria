<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Cloudinary\Cloudinary;
use Cloudinary\Api\Upload\UploadApi;

class MarcaController extends Controller
{
    public function index(Request $request)
    {

        if (!request()->user()->tienePermiso('marca.listar')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para listar marcas.');
        }

        return inertia('Marcas/Index', [
            'marcas' => Marca::query()
            ->when(
                $request->busqueda,
                function ($query) use ($request) {
                $query->whereRaw('LOWER(nombre) like LOWER(?)', ['%' . $request->busqueda . '%']);
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
        if (!request()->user()->tienePermiso('marca.crear')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para listar marcas.');
        }

        return inertia('Marcas/Create');
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
                    'folder' => 'marcas',
                    'resource_type' => 'image'
                ]
            );
            
            $data['foto'] = $result['secure_url'];
        }

        Marca::create($data);

        return redirect()->route('marcas.index')->with('success', 'Marca creada correctamente.');
    }

    public function edit(Marca $marca)
    {
        if (!request()->user()->tienePermiso('marca.editar')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para editar marcas.');
        }
    
        return inertia('Marcas/Edit', compact('marca'));
    }

    public function update(Request $request, Marca $marca)
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
                    'folder' => 'marcas',
                    'resource_type' => 'image'
                ]
            );
            
            $data['foto'] = $result['secure_url'];
        }

        $marca->update($data);

        return redirect()->route('marcas.index')->with('success', 'Marca actualizada correctamente.');
    }

    public function destroy(Marca $marca)
    {
        try {
            // Verificar si tiene motores activos
            $tieneMotoresActivos = DB::table('motores')
                ->where('marca_id', $marca->id)
                ->whereNull('deleted_at')
                ->exists();

            if ($tieneMotoresActivos) {
                return redirect()->route('marcas.index')->with('error', 'No se puede eliminar esta marca porque tiene motores asociados. Por favor, elimine o reasigne los motores antes de continuar.');
            }

            // Si no tiene motores activos, hacer soft delete
            $marca->delete();
            return redirect()->route('marcas.index')->with('success', 'Marca eliminada correctamente.');
            
        } catch (\Illuminate\Database\QueryException $e) {
            // Capturar errores de restricción de base de datos
            if ($e->getCode() === '23503' || strpos($e->getMessage(), 'foreign key') !== false) {
                return redirect()->route('marcas.index')->with('error', 'No se puede eliminar esta marca porque tiene registros relacionados en el sistema. Por favor, elimine primero los registros dependientes.');
            }
            
            return redirect()->route('marcas.index')->with('error', 'Ocurrió un error al intentar eliminar la marca. Por favor, intente nuevamente.');
            
        } catch (\Exception $e) {
            return redirect()->route('marcas.index')->with('error', 'Error inesperado. Por favor, contacte al administrador.');
        }
    }
}
