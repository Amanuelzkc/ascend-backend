<?php

namespace App\Http\Controllers;

use App\Models\Insight;
use Illuminate\Http\Request;

class InsightController extends Controller
{
    public function index()
    {
        return Insight::latest()->get();
    }

    public function store(Request $request)
    {
        \Log::info('Insight Store Request: ', $request->all());
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:insights,slug|max:255',
            'excerpt' => 'required|string',
            'content' => 'required|string',
            'icon_name' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'read_time' => 'required|string|max:255',
            'published' => 'boolean',
            'featured' => 'boolean',
            'image_url' => 'nullable',
            'scheduled_at' => 'nullable|date',
        ], [
            'image_url.url' => 'DEBUG: THIS IS THE INSIGHT URL ERROR',
        ]);

        $insight = Insight::create($validated);
        return response()->json($insight, 201);
    }

    public function show($identifier)
    {
    $insight = is_numeric($identifier)
        ? Insight::findOrFail($identifier)
        : Insight::where('slug', $identifier)->firstOrFail();
        
    return $insight;
    }

    public function update(Request $request, Insight $insight)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|unique:insights,slug,'.$insight->id.'|max:255',
            'excerpt' => 'sometimes|string',
            'content' => 'sometimes|string',
            'icon_name' => 'sometimes|string|max:255',
            'author' => 'sometimes|string|max:255',
            'read_time' => 'sometimes|string|max:255',
            'published' => 'boolean',
            'featured' => 'boolean',
            'image_url' => 'nullable|string',
            'scheduled_at' => 'nullable|date',
        ]);

        $insight->update($validated);
        return response()->json($insight);
    }

    public function destroy(Insight $insight)
    {
        $insight->delete();
        return response()->json(null, 204);
    }
}
