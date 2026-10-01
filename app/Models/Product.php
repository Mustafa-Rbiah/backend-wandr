<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'category_id', 
        'name', 
        'slug', 
        'description', 
        'price', 
        'image',  
        'video', 
        'stock'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function blogs()
{
    return $this->hasMany(Blog::class);
}

    public function reviews() {
        return $this->hasMany(Review::class);
    }
    public function orderItems(){
    return $this->hasMany(OrderItem::class);
}

}
