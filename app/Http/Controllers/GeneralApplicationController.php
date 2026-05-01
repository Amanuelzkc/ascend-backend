<?php

namespace App\Http\Controllers;

use App\Models\GeneralApplication;
use App\Models\Notification;
use Illuminate\Http\Request;

class GeneralApplicationController extends Controller
{
    public function index()
    {
        return GeneralApplication::latest()->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fullName' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:255',
            'resumeUrl' => 'required|url',
            'message' => 'nullable|string',
        ]);

        $validated['status'] = 'New';
        $validated['trackingCode'] = uniqid('GEN-');

        $application = GeneralApplication::create($validated);

        // Create notification for admin
        Notification::create([
            'type' => 'cv',
            'title' => 'New General CV',
            'message' => "{$application->fullName} submitted a general CV",
            'link' => '/admin',
            'read' => false
        ]);
        return response()->json($application, 201);
    }

    public function show(GeneralApplication $generalApplication)
    {
        return $generalApplication;
    }

    public function update(Request $request, GeneralApplication $generalApplication)
    {
        $validated = $request->validate([
            'status' => 'required|string|max:255',
        ]);

        $generalApplication->update($validated);
        return response()->json($generalApplication);
    }

    public function destroy(GeneralApplication $generalApplication)
    {
        $generalApplication->delete();
        return response()->json(null, 204);
    }
}
