<?php

namespace App\Http\Controllers;

use App\Models\SocialNetwork;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SocialNetworkController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'platform' => 'required|string|max:50',
            'link' => 'required|url|max:255',
        ], [
            'platform.required' => 'La plataforma es obligatoria',
            'link.required' => 'El enlace es obligatorio',
            'link.url' => 'Debe ser una URL válida',
        ]);

        $profile = Auth::user()->professionalProfile;

        if (!$profile) {
            return back()->with('error', 'No tienes un perfil profesional.');
        }

        SocialNetwork::create([
            'professional_profile_id' => $profile->id,
            'platform' => $request->platform,
            'link' => $request->link,
        ]);

        return back()->with('success', 'Red social agregada correctamente.');
    }

    public function destroy($id)
    {
        $socialNetwork = SocialNetwork::findOrFail($id);
        
        if ($socialNetwork->profile->user_id !== Auth::id()) {
            return back()->with('error', 'No autorizado.');
        }

        $socialNetwork->delete();

        return back()->with('success', 'Red social eliminada correctamente.');
    }
}