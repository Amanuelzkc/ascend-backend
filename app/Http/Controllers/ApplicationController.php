<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ApplicationController extends Controller
{
    public function index()
    {
        return Application::with('jobPosting')->latest()->get();
    }

    public function store(Request $request, $jobId = null)
    {
        try {
            $validated = $request->validate([
                'fullName' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:255',
                'location' => 'nullable|string|max:255',
                'currentRole' => 'required|string|max:255',
                'experience' => 'required|string|max:255',
                'coverLetter' => 'nullable|string',
                'resume' => 'nullable|file|mimes:pdf,doc,docx|max:10240', // Increased to 10MB
                'resumeUrl' => 'nullable|string',
                'jobId' => 'nullable|integer',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Job Application Validation Failed', [
                'errors' => $e->errors(),
                'request' => $request->all()
            ]);
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        try {
            $finalJobId = $jobId ?? $request->input('jobId');

            if (!$finalJobId) {
                return response()->json(['message' => 'Job ID is required'], 422);
            }

            // Handle File Upload if present
            $resumeUrl = $request->input('resumeUrl');
            if ($request->hasFile('resume')) {
                $file = $request->file('resume');
                $filename = time() . '-' . Str::slug($file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('resumes', $filename, 'public');
                
                if (!$path) {
                    throw new \Exception('Failed to store resume file on disk');
                }
                
                $resumeUrl = Storage::url($path);
            }

            if (!$resumeUrl) {
                return response()->json(['message' => 'Resume is required'], 422);
            }

            $application = Application::create([
                'job_posting_id' => $finalJobId,
                'fullName' => $validated['fullName'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'location' => $validated['location'] ?? null,
                'currentRole' => $validated['currentRole'],
                'experience' => $validated['experience'],
                'coverLetter' => $validated['coverLetter'] ?? null,
                'resumeUrl' => $resumeUrl,
                'status' => 'Pending',
                'trackingCode' => uniqid('APP-'),
            ]);

            // Create notification for admin
            Notification::create([
                'type' => 'application',
                'title' => 'New Job Application',
                'message' => "{$application->fullName} applied for {$application->jobTitle}",
                'link' => '/admin', // Link to admin dashboard
                'read' => false
            ]);

            return response()->json([
                'success' => true,
                'data' => $application
            ], 201);
        } catch (\Exception $e) {
            Log::error('Job Application Submission Error: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->except('resume')
            ]);
            return response()->json([
                'message' => 'Failed to submit application: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Application $application)
    {
        return $application->load('jobPosting');
    }

    public function update(Request $request, Application $application)
    {
        $validated = $request->validate([
            'status' => 'required|string|max:255',
        ]);

        $application->update($validated);
        return response()->json($application);
    }

    public function destroy(Application $application)
    {
        $application->delete();
        return response()->json(null, 204);
    }
}
