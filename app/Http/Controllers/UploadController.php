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
}
