<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Cloudinary\Cloudinary;
use Cloudinary\Api\Upload\UploadApi;


class ClienteController extends Controller
{
    public function index(Request $request)
    {
        if (!request()->user()->tienePermiso('cliente.listar')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para listar clientes.');
        }

        return inertia('Clientes/Index', [
            'clientes' => Cliente::query()
            ->when(
                $request->busqueda,
                function ($query) use ($request) {
                    $query->whereRaw('LOWER(nombre) like LOWER(?)', ['%' . $request->busqueda . '%'])
                          ->orWhereRaw('LOWER(telefono) like LOWER(?)', ['%' . $request->busqueda . '%']);
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
        if (!request()->user()->tienePermiso('cliente.crear')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para crear clientes.');
        }

        return inertia('Clientes/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'foto' => 'nullable|image|max:2048',
        ]);

        $data = $request->except('foto');
        
        if ($request->hasFile('foto')) {
            // Configurar Cloudinary
            $cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => config('cloudinary.cloud_name'),
                    'api_key' => config('cloudinary.api_key'),
                    'api_secret' => config('cloudinary.api_secret'),
                ],
            ]);

            // Subir imagen a Cloudinary
            $uploadApi = new UploadApi();
            $result = $uploadApi->upload(
                $request->file('foto')->getRealPath(),
                [
                    'folder' => 'clientes',
                    'resource_type' => 'image'
                ]
            );
            
            // Guardar la URL de Cloudinary
            $data['foto'] = $result['secure_url'];
        }

        Cliente::create($data);

        return redirect()->route('clientes.index')->with('success', 'Cliente creado correctamente.');
    }

    public function edit(Cliente $cliente)
    {
        if (!request()->user()->tienePermiso('cliente.editar')) {
            return redirect()->route('dashboard')->with('error', 'No tenés permiso para editar clientes.');
        }
    
        return inertia('Clientes/Edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'foto' => 'nullable|image|max:2048',
        ]);

        $data = $request->except('foto');
        
        if ($request->hasFile('foto')) {
            // Configurar Cloudinary
            $cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => config('cloudinary.cloud_name'),
                    'api_key' => config('cloudinary.api_key'),
                    'api_secret' => config('cloudinary.api_secret'),
                ],
            ]);

            // Subir imagen a Cloudinary
            $uploadApi = new UploadApi();
            $result = $uploadApi->upload(
                $request->file('foto')->getRealPath(),
                [
                    'folder' => 'clientes',
                    'resource_type' => 'image'
                ]
            );
            
            // Guardar la URL de Cloudinary
            $data['foto'] = $result['secure_url'];
        }

        $cliente->update($data);

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente)
    {
        try {
            // Verificar órdenes de trabajo activas (no eliminadas)
            $tieneOrdenesActivas = DB::table('orden_trabajos')
                ->where('cliente_id', $cliente->id)
                ->whereNull('deleted_at')
                ->exists();

            if ($tieneOrdenesActivas) {
                return back()->with('error', 'No se puede eliminar este cliente porque tiene órdenes de trabajo activas. Por favor, elimine primero todas las órdenes de trabajo asociadas.');
            }

            // Solo hacer soft delete (no eliminación física) para mantener historial
            $cliente->delete();
            return back()->with('success', 'Cliente desactivado correctamente.');
            
        } catch (\Illuminate\Database\QueryException $e) {
            // Capturar errores de restricción de base de datos
            if ($e->getCode() === '23503' || strpos($e->getMessage(), 'foreign key') !== false) {
                return back()->with('error', 'No se puede eliminar este cliente porque tiene registros históricos asociados. El cliente quedará desactivado pero se mantendrá en el historial del sistema.');
            }
            
            return back()->with('error', 'Ocurrió un error al intentar eliminar el cliente. Por favor, intente nuevamente.');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error inesperado. Por favor, contacte al administrador.');
        }
    }
}
