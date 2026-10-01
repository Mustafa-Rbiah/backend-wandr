<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
{
    
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,
            'category'    => $this->category,
            'date'        => $this->created_at ? $this->created_at->format('d M, Y') : null,
            'image'       => $this->image
                ? (str_starts_with($this->image, 'http') ? $this->image : url('storage/' . $this->image))
                : null,
            'associated_product' => $this->whenLoaded('product', function () {
                return new ProductResource($this->product);
            }),
        ];
    }
}