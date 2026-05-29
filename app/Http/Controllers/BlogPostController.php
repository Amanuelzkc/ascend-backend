<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;

class BlogPostController extends Controller
{
    public function index()
    {
        return BlogPost::latest()->get();
    }

    public function store(Request $request)
    {
        \Log::info('Blog Store Request: ', $request->all());
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:blog_posts,slug|max:255',
            'excerpt' => 'required|string',
            'content' => 'required|string',
            'author' => 'required|string|max:255',
            'read_time' => 'required|string|max:255',
            'published' => 'boolean',
            'scheduled_at' => 'nullable|date',
            'image_url' => 'nullable',
        ], [
            'image_url.url' => 'DEBUG: THIS IS THE URL ERROR',
        ]);

        $post = BlogPost::create($validated);
        return response()->json($post, 201);
    }

    public function show($identifier)
    {
    $post = is_numeric($identifier)
        ? BlogPost::findOrFail($identifier)
        : BlogPost::where('slug', $identifier)->firstOrFail();
    return $post;
    }

    public function update(Request $request, BlogPost $blog)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|unique:blog_posts,slug,'.$blog->id.'|max:255',
            'excerpt' => 'sometimes|string',
            'content' => 'sometimes|string',
            'author' => 'sometimes|string|max:255',
            'read_time' => 'sometimes|string|max:255',
            'published' => 'boolean',
            'scheduled_at' => 'nullable|date',
            'image_url' => 'nullable',
        ]);

        $blog->update($validated);
        return response()->json($blog);
    }

    public function destroy(BlogPost $blog)
    {
        $blog->delete();
        return response()->json(null, 204);
    }
}
