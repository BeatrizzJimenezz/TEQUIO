<?php

namespace App\Http\Controllers;

use App\Models\ProfessionalProfile;
use App\Models\AcademicTraining;
use App\Models\SocialNetwork;
use App\Models\EventComponent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProfessionalProfileController extends Controller
{
    // Ver perfil profesional propio
    public function show()
    {
        $user = auth()->user();
        
        // Obtener o crear perfil si no existe
        $profile = $user->professionalProfile()->firstOrCreate([]);
        $profile->load(['academicTrainings', 'socialNetworks']);
        
        // Obtener charlas/talleres donde el usuario es ponente
        $activities = EventComponent::where('speaker_id', $profile->id)
            ->where('proposal_status', 'approved')
            ->with(['event', 'schedules'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('professional-profile.show', compact('profile', 'activities'));
    }

    // Ver perfil público de otro usuario
    public function showPublic($id)
    {
        $profile = ProfessionalProfile::with(['academicTrainings', 'socialNetworks', 'user'])->findOrFail($id);
        
        // Obtener actividades aprobadas
        $activities = EventComponent::where('speaker_id', $profile->id)
            ->where('proposal_status', 'approved')
            ->with(['event', 'schedules'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('professional-profile.public', compact('profile', 'activities'));
    }

    // Editar perfil profesional
    public function edit()
    {
        $profile = auth()->user()->professionalProfile()->firstOrCreate([]);
        $profile->load(['academicTrainings', 'socialNetworks']);
        
        return view('professional-profile.edit', compact('profile'));
    }

    // Actualizar información del perfil
    public function update(Request $request)
    {
        $validated = $request->validate([
            'about_me' => 'nullable|string|max:5000',
            'current_workplace' => 'nullable|string|max:255',
            'skills' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $profile = auth()->user()->professionalProfile()->firstOrCreate([]);
            $profile->update($validated);

            DB::commit();

            return redirect()->route('professional-profile.show')
                ->with('success', 'Perfil profesional actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error al actualizar perfil: ' . $e->getMessage());
        }
    }

    // Subir foto de perfil
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Eliminar foto anterior si existe
        if (auth()->user()->profile_photo) {
            Storage::disk('public')->delete(auth()->user()->profile_photo);
        }

        // Guardar nueva foto
        $path = $request->file('profile_photo')->store('profile_photos', 'public');

        auth()->user()->update([
            'profile_photo' => $path
        ]);

        return back()->with('success', 'Foto de perfil actualizada correctamente.');
    }

    // Eliminar foto de perfil
    public function deletePhoto(Request $request)
    {
        try {
            $user = auth()->user();
            
            if ($user->profile_photo) {
                if (Storage::disk('public')->exists($user->profile_photo)) {
                    Storage::disk('public')->delete($user->profile_photo);
                }

                $user->profile_photo = null;
                $user->save();

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Foto de perfil eliminada correctamente.'
                    ]);
                }

                return redirect()->back()->with('success', 'Foto de perfil eliminada correctamente.');
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay foto para eliminar.'
                ]);
            }

            return redirect()->back()->with('error', 'No hay foto para eliminar.');
            
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar la foto: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al eliminar la foto: ' . $e->getMessage());
        }
    }

    // Guardar formación académica
    public function storeAcademicTraining(Request $request)
    {
        $validated = $request->validate([
            'institution' => 'required|string|max:255',
            'degree' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'description' => 'nullable|string|max:1000',
        ], [
            'institution.required' => 'La institución es obligatoria.',
            'degree.required' => 'El título es obligatorio.',
            'start_date.required' => 'La fecha de inicio es obligatoria.',
            'end_date.after' => 'La fecha de fin debe ser posterior a la de inicio.',
        ]);

        try {
            // Usamos firstOrCreate para asegurar consistencia
            $profile = auth()->user()->professionalProfile()->firstOrCreate([]);
            $profile->academicTrainings()->create($validated);

            return redirect()->route('professional-profile.edit')
                ->with('success', 'Formación académica agregada exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al agregar formación: ' . $e->getMessage());
        }
    }

    // Eliminar formación académica
    public function destroyAcademicTraining($id)
    {
        try {
            $training = AcademicTraining::where('professional_profile_id', auth()->user()->professionalProfile->id)
                ->findOrFail($id);
            
            $training->delete();

            return redirect()->route('professional-profile.edit')
                ->with('success', 'Formación académica eliminada exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar la formación académica.');
        }
    }

    // Guardar red social
    public function storeSocialNetwork(Request $request)
    {
        $validated = $request->validate([
            'platform' => 'required|string|max:255',
            'link' => 'required|url|max:500',
        ], [
            'platform.required' => 'La plataforma es obligatoria.',
            'link.required' => 'El enlace es obligatorio.',
            'link.url' => 'Debe ser una URL válida.',
        ]);

        try {
            $profile = auth()->user()->professionalProfile()->firstOrCreate([]);
            $profile->socialNetworks()->create($validated);

            return redirect()->route('professional-profile.edit')
                ->with('success', 'Red social agregada exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al agregar red social: ' . $e->getMessage());
        }
    }

    // Eliminar red social
    public function destroySocialNetwork($id)
    {
        try {
            $socialNetwork = SocialNetwork::where('professional_profile_id', auth()->user()->professionalProfile->id)
                ->findOrFail($id);
            
            $socialNetwork->delete();

            return redirect()->route('professional-profile.edit')
                ->with('success', 'Red social eliminada exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar la red social.');
        }
    }
}