<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    // Verificar permisos de usuario (solo Administrador)
    private function checkPermissions()
    {
        if (!auth()->check()) {
            abort(401, 'Debes iniciar sesión.');
        }

        if (!auth()->user()->hasRole('Administrador')) {
            abort(403, 'Solo el administrador puede gestionar etiquetas.');
        }
    }

    // Listar todas las etiquetas
    public function index()
    {
        $this->checkPermissions();
        
        $tags = Tag::orderBy('name')->get();
        return view('tags.index', compact('tags'));
    }

    // Almacenar una nueva etiqueta
    public function store(Request $request)
    {
        $this->checkPermissions();
        
        $request->validate([
            'name' => 'required|string|max:255|unique:tags,name'
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.unique' => 'Esta etiqueta ya existe.'
        ]);

        Tag::create(['name' => $request->name]);

        return redirect()->route('tags.index')
            ->with('success', 'Etiqueta creada exitosamente.');
    }

    // Actualizar una etiqueta existente
    public function update(Request $request, Tag $tag)
    {
        $this->checkPermissions();
        
        $request->validate([
            'name' => 'required|string|max:255|unique:tags,name,' . $tag->id
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.unique' => 'Esta etiqueta ya existe.'
        ]);

        $tag->update(['name' => $request->name]);

        return redirect()->route('tags.index')
            ->with('success', 'Etiqueta actualizada exitosamente.');
    }

    // Eliminar una etiqueta
    public function destroy(Tag $tag)
    {
        $this->checkPermissions();
        
        try {
            $tag->delete();
            return redirect()->route('tags.index')
                ->with('success', 'Etiqueta eliminada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('tags.index')
                ->with('error', 'No se puede eliminar la etiqueta porque está en uso.');
        }
    }
}