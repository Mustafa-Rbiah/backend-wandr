<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
{
    return [
        'id' => $this->id,
        'name' => $this->name,
        'slug' => $this->slug,
        'price' => (float) $this->price,
        'description' => $this->description,
        'stock' => $this->stock,
        'image' => $this->image ? (str_starts_with($this->image, 'http') ? $this->image : asset('storage/' . $this->image)) : null,
        'video' => $this->video ? (str_starts_with($this->video, 'http') ? $this->video : asset('storage/' . $this->video)) : null,
        'reviews_count' => $this->reviews()->count(),
        'average_rating' => round($this->reviews()->avg('rating'), 1) ?: 0,
        'category' => $this->category->name, 
        'category_id' => $this->category_id,
        'date' => $this->created_at->format('d M, Y'),
        'reviews' => $this->reviews->map(function($review) {
    return [
        'id' => $review->id,
        'user_name' => $review->user_name,
        'rating' => $review->rating,
        'comment' => $review->comment,
        'date' => $review->created_at->diffForHumans(), 
        'avatar' => $review->user ? $review->user->avatar : null, 

    ];
}),
    ];
}
}
