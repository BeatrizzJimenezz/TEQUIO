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
    // View own professional profile
    public function show()
    {
        $profile = auth()->user()->professionalProfile()->with(['academicTrainings', 'socialNetworks'])->first();
        
        if (!$profile) {
            $profile = auth()->user()->professionalProfile()->create([]);
        }
        
        // Get talks/workshops where user has been a speaker
        $activities = EventComponent::where('speaker_id', $profile->id)
            ->where('proposal_status', 'approved')
            ->with(['event', 'schedules'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('professional-profile.show', compact('profile', 'activities'));
    }

    // View public profile of another user
    public function showPublic($id)
    {
        $profile = ProfessionalProfile::with(['academicTrainings', 'socialNetworks', 'user'])->findOrFail($id);
        
        // Get approved talks/workshops
        $activities = EventComponent::where('speaker_id', $profile->id)
            ->where('proposal_status', 'approved')
            ->with(['event', 'schedules'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('professional-profile.public', compact('profile', 'activities'));
    }

    // Edit professional profile
    public function edit()
    {
        $profile = auth()->user()->professionalProfile;
        
        if (!$profile) {
            $profile = auth()->user()->professionalProfile()->create([]);
        }
        
        $profile->load(['academicTrainings', 'socialNetworks']);
        
        return view('professional-profile.edit', compact('profile'));
    }

    // Update professional profile
    public function update(Request $request)
    {
        $validated = $request->validate([
            'about_me' => 'nullable|string|max:5000',
            'current_workplace' => 'nullable|string|max:255',
            'skills' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $profile = auth()->user()->professionalProfile;
            
            if (!$profile) {
                $profile = auth()->user()->professionalProfile()->create([]);
            }
            
            $profile->update($validated);

            DB::commit();

            return redirect()->route('professional-profile.show')
                ->with('success', 'Professional profile updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error updating profile: ' . $e->getMessage());
        }
    }

    // Upload profile photo
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

        // Actualizar el usuario
        auth()->user()->update([
            'profile_photo' => $path
        ]);

        return back()->with('success', 'Foto de perfil actualizada correctamente');
    }

    // Delete profile photo
    public function deletePhoto(Request $request)
    {
        try {
            $user = auth()->user();
            
            if ($user->profile_photo) {
                // Eliminar archivo físico si existe
                if (Storage::disk('public')->exists($user->profile_photo)) {
                    Storage::disk('public')->delete($user->profile_photo);
                }

                // Actualizar base de datos
                $user->profile_photo = null;
                $user->save();

                // Si es una petición AJAX, devolver JSON
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Foto de perfil eliminada correctamente.'
                    ]);
                }

                return redirect()->back()->with('success', 'Foto de perfil eliminada correctamente.');
            }

            // Si es AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay foto para eliminar.'
                ]);
            }

            return redirect()->back()->with('error', 'No hay foto para eliminar.');
            
        } catch (\Exception $e) {
            // Si es AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar la foto: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al eliminar la foto: ' . $e->getMessage());
        }
    }

    // Save academic training
    public function storeAcademicTraining(Request $request)
    {
        $validated = $request->validate([
            'institution' => 'required|string|max:255',
            'degree' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'description' => 'nullable|string|max:1000',
        ], [
            'institution.required' => 'Institution is required.',
            'degree.required' => 'Degree is required.',
            'start_date.required' => 'Start date is required.',
            'end_date.after' => 'End date must be after start date.',
        ]);

        try {
            $profile = auth()->user()->getOrCreateProfessionalProfile();
            $profile->academicTrainings()->create($validated);

            return redirect()->route('professional-profile.edit')
                ->with('success', 'Academic training added successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error adding training: ' . $e->getMessage());
        }
    }

    // Delete academic training
    public function destroyAcademicTraining($id)
    {
        try {
            $training = AcademicTraining::where('professional_profile_id', auth()->user()->professionalProfile->id)
                ->findOrFail($id);
            
            $training->delete();

            return redirect()->route('professional-profile.edit')
                ->with('success', 'Academic training deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting training.');
        }
    }

    // Save social network
    public function storeSocialNetwork(Request $request)
    {
        $validated = $request->validate([
            'platform' => 'required|string|max:255',
            'link' => 'required|url|max:500',
        ], [
            'platform.required' => 'Platform is required.',
            'link.required' => 'Link is required.',
            'link.url' => 'Must be a valid URL.',
        ]);

        try {
            $profile = auth()->user()->getOrCreateProfessionalProfile();
            $profile->socialNetworks()->create($validated);

            return redirect()->route('professional-profile.edit')
                ->with('success', 'Social network added successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error adding social network: ' . $e->getMessage());
        }
    }

    // Delete social network
    public function destroySocialNetwork($id)
    {
        try {
            $socialNetwork = SocialNetwork::where('professional_profile_id', auth()->user()->professionalProfile->id)
                ->findOrFail($id);
            
            $socialNetwork->delete();

            return redirect()->route('professional-profile.edit')
                ->with('success', 'Social network deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting social network.');
        }
    }
}