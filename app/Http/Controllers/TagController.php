<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    // Helper method to check permissions
    private function checkPermissions()
    {
        if (!auth()->check()) {
            abort(401, 'You must be logged in.');
        }
        
        if (!auth()->user()->hasAnyRole(['Administrador', 'Organizador'])) {
            abort(403, 'You do not have permission to access this section.');
        }
    }

    // List all tags
    public function index()
    {
        $this->checkPermissions();
        
        $tags = Tag::orderBy('name')->get();
        return view('tags.index', compact('tags'));
    }

    // Store a new tag
    public function store(Request $request)
    {
        $this->checkPermissions();
        
        $request->validate([
            'name' => 'required|string|max:255|unique:tags,name'
        ], [
            'name.required' => 'The name is required.',
            'name.unique' => 'This tag already exists.'
        ]);

        Tag::create(['name' => $request->name]);

        return redirect()->route('tags.index')
            ->with('success', 'Tag created successfully.');
    }

    // Update an existing tag
    public function update(Request $request, Tag $tag)
    {
        $this->checkPermissions();
        
        $request->validate([
            'name' => 'required|string|max:255|unique:tags,name,' . $tag->id
        ], [
            'name.required' => 'The name is required.',
            'name.unique' => 'This tag already exists.'
        ]);

        $tag->update(['name' => $request->name]);

        return redirect()->route('tags.index')
            ->with('success', 'Tag updated successfully.');
    }

    // Delete a tag
    public function destroy(Tag $tag)
    {
        $this->checkPermissions();
        
        try {
            $tag->delete();
            return redirect()->route('tags.index')
                ->with('success', 'Tag deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('tags.index')
                ->with('error', 'Cannot delete the tag because it is in use.');
        }
    }
}