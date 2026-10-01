<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ReviewController extends Controller
{
    public function store(Request $request, \App\Models\Product $product) 
{
    $validated = $request->validate([
        'user_name' => 'required|string',
        'rating'    => 'required|integer|min:1|max:5',
        'comment'   => 'nullable|string',
    ]);

    $product->reviews()->create($validated);

    return response()->json(['success' => true, 'message' => 'Review added!']);
}
}