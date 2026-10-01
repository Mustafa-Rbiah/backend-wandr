<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Blog;
use App\Http\Resources\BlogResource;

class BlogController extends Controller
{
    /**
     * Display a listing of the blogs.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $blogs = Blog::with('product')->latest()->get();
        return BlogResource::collection($blogs);
    }

    /**
     * Store a newly created blog in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'category' => 'required|string',
            'product_id' => 'required|exists:products,id',
            'image' => 'required|image',
            'date' => 'required|date',
        ]);

        // Handle and store image
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('blogs', 'public');
        }

        $blog = Blog::create($validated);

        // Return the created blog resource
        return response()->json([
            'success' => true,
            'data' => new BlogResource($blog)
        ], 201);
    }

    /**
     * Update the specified blog in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Blog $blog)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'category' => 'required|string',
            'product_id' => 'required|exists:products,id',
            'image' => 'nullable|image',
            'date' => 'sometimes|date',
        ]);

        // Handle image replacement if a new image is uploaded
        if ($request->hasFile('image')) {
            if ($blog->image && !str_starts_with($blog->image, 'http')) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($blog->image);
            }
            $validated['image'] = $request->file('image')->store('blogs', 'public');
        } else {
            unset($validated['image']);
        }

        $blog->update($validated);

        return response()->json([
            'success' => true,
            'data' => new BlogResource($blog->fresh())
        ]);
    }

    public function destroy(Blog $blog)
    {
        try {
            if ($blog->image && !str_starts_with($blog->image, 'http')) {
                // Remove the full storage URL, keep the path relative to 'public'
                $path = str_replace(url('storage') . '/', '', $blog->image);
                \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
            }

            $blog->delete();

            return response()->json([
                'success' => true,
                'message' => 'Story archived and removed.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}