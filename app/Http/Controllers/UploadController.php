<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function uploadImage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp,avif|max:10240',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            \Log::info('Upload proxy check: ', [
                'mime' => $file->getMimeType(),
                'client_mime' => $file->getClientMimeType(),
                'extension' => $file->getClientOriginalExtension(),
                'name' => $file->getClientOriginalName()
            ]);
            $filename = time() . '-' . Str::slug($file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
            
            // Store in public/uploads (symlinked to storage/app/public/uploads)
            $path = $file->storeAs('uploads', $filename, 'public');
            
            return response()->json([
                'success' => true,
                'url' => Storage::url($path),
            ], 201);
        }

        return response()->json(['message' => 'No file uploaded'], 400);
    }

    public function uploadResume(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf,doc,docx|max:5120',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '-' . Str::slug($file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
            
            $path = $file->storeAs('resumes', $filename, 'public');
            
            return response()->json([
                'success' => true,
                'url' => Storage::url($path),
            ], 201);
        }

        return response()->json(['message' => 'No file uploaded'], 400);
    }

   public function downloadResume(Request $request)
{
    $url = $request->query('url');
    $filename = $request->query('filename', 'resume.pdf');

    if (!$url) {
        return response()->json(['message' => 'No URL provided'], 400);
    }

    // Handle relative storage paths
    if (str_starts_with($url, '/storage/')) {
        $relativePath = str_replace('/storage/', '', $url);
        $fullPath = storage_path('app/public/' . $relativePath);
        
        if (!file_exists($fullPath)) {
            return response()->json(['message' => 'File not found'], 404);
        }
        
        return response()->download($fullPath, $filename);
    }

    // Handle full URLs
    $contents = file_get_contents($url);
    return response($contents, 200, [
        'Content-Type' => 'application/octet-stream',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ]);
}
}
