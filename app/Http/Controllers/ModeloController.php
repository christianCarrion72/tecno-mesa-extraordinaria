<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use Illuminate\Http\Request;
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

        $modelo->delete();

        return redirect()->route('modelos.index')->with('success', 'Modelo eliminado correctamente.');
    }
}
