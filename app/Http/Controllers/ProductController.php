<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function index()
    {
    $products = Product::with('category')->latest()->get();
    return ProductResource::collection($products);
    }
    
    public function updatePrice(\Illuminate\Http\Request $request, Product $product)
    {
        $validated = $request->validate([
            'price' => 'required|numeric|min:0',
        ]);

        $product->price = $validated['price'];
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Price updated successfully',
            'new_price' => $product->price
        ]);
    }
    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price'       => 'required|numeric',
            'stock'       => 'required|integer',
            'image'       => 'required|image|mimes:jpeg,png,jpg,webp',
            'video'       => 'nullable|mimes:mp4,mov,ogg|max:50240', // حد 30MB
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products/images', 'public');
        }

        if ($request->hasFile('video')) {
            $validated['video'] = $request->file('video')->store('products/videos', 'public');
        }

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']) . '-' . rand(100, 999);
   

        $product = Product::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Treasure added to vault!',
            'data'    => $product
        ], 201);
    }

    public function destroy(Product $product)
    {
        try {
            if ($product->image && \Storage::disk('public')->exists($product->image)) {
                \Storage::disk('public')->delete($product->image);
            }
            if ($product->video && \Storage::disk('public')->exists($product->video)) {
                \Storage::disk('public')->delete($product->video);
            }
            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Treasure removed from vault.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove treasure: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function update(\Illuminate\Http\Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'        => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price'       => 'required|numeric',
            'stock'       => 'required|integer',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp',
            'video'       => 'nullable|mimes:mp4,mov,ogg|max:50240',
        ]);

        // Handle image replacement (if provided)
        if ($request->hasFile('image')) {
            if ($product->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($product->image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products/images', 'public');
        }

        // Handle video replacement (if provided)
        if ($request->hasFile('video')) {
            if ($product->video && \Illuminate\Support\Facades\Storage::disk('public')->exists($product->video)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($product->video);
            }
            $validated['video'] = $request->file('video')->store('products/videos', 'public');
        }

        // Optionally refresh slug if name is updated
        if (isset($validated['name']) && $validated['name'] !== $product->name) {
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']) . '-' . rand(100, 999);
        }

        $product->update($validated);

        return response()->json(['success' => true, 'data' => $product->fresh()]);
    }
    public function show(Product $product)
    {
        return new \App\Http\Resources\ProductResource($product->load(['category', 'reviews']));
    }

    public function getTopRated()
    {
        $products = Product::whereHas('reviews')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->orderByDesc('reviews_avg_rating')
            ->orderByDesc('reviews_count')
            ->take(10)
            ->get(['id', 'name', 'image', 'slug']);

        // Remap output to use expected keys for frontend (average/count)
        $data = $products->map(function ($product) {
            return [
                'id'      => $product->id,
                'name'    => $product->name,
                'image'   => $product->image,
                'slug'    => $product->slug,
                'average' => round($product->reviews_avg_rating ?? 0, 1),
                'count'   => $product->reviews_count,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }
}

