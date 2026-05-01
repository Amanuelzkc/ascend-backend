<?php

namespace App\Http\Controllers;

use App\Models\JobPosting;
use Illuminate\Http\Request;

class JobPostingController extends Controller
{
    public function index()
    {
        return JobPosting::latest()->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:job_postings,slug|max:255',
            'department' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'experience' => 'nullable|string',
            'description' => 'required|string',
            'requirements' => 'nullable|array',
            'responsibilities' => 'nullable|array',
            'salary_range' => 'nullable|string',
            'published' => 'boolean',
            'scheduled_at' => 'nullable|date',
        ]);

        $job = JobPosting::create($validated);
        return response()->json($job, 201);
    }

    public function show($slug)
    {
        return JobPosting::where('slug', $slug)->firstOrFail();
    }

    public function update(Request $request, JobPosting $job)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|unique:job_postings,slug,'.$job->id.'|max:255',
            'department' => 'sometimes|string|max:255',
            'location' => 'sometimes|string|max:255',
            'type' => 'sometimes|string|max:255',
            'experience' => 'nullable|string',
            'description' => 'sometimes|string',
            'requirements' => 'nullable|array',
            'responsibilities' => 'nullable|array',
            'salary_range' => 'nullable|string',
            'published' => 'boolean',
            'scheduled_at' => 'nullable|date',
        ]);

        $job->update($validated);
        return response()->json($job);
    }

    public function destroy(JobPosting $job)
    {
        $job->delete();
        return response()->json(null, 204);
    }
}
