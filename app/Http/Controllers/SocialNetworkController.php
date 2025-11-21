<?php

namespace App\Http\Controllers;

use App\Models\SocialNetwork;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SocialNetworkController extends Controller
{
    // Agregar una nueva red social al perfil
    public function store(Request $request)
    {
        $validated = $request->validate([
            'platform' => 'required|string|max:50',
            'link' => 'required|url|max:255',
        ], [
            'platform.required' => 'La plataforma es obligatoria.',
            'link.required' => 'El enlace es obligatorio.',
            'link.url' => 'Debe ser una URL válida.',
        ]);

        // Asegurar que existe el perfil antes de agregar la red
        $profile = Auth::user()->professionalProfile()->firstOrCreate([]);

        $profile->socialNetworks()->create([
            'platform' => $validated['platform'],
            'link' => $validated['link'],
        ]);

        return back()->with('success', 'Red social agregada correctamente.');
    }

    // Eliminar una red social
    public function destroy($id)
    {
        // Verificar perfil del usuario actual
        $profile = Auth::user()->professionalProfile;

        if (!$profile) {
            abort(403, 'No autorizado.');
        }

        // Buscar la red social asegurando que pertenezca al usuario
        $socialNetwork = SocialNetwork::where('professional_profile_id', $profile->id)
            ->where('id', $id)
            ->firstOrFail();

        $socialNetwork->delete();

        return back()->with('success', 'Red social eliminada correctamente.');
    }
}