<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Blog;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $product = Product::find(1);

        Blog::create([
            'title'       => 'The Secrets of 18k Gold',
            'description' => 'In this article, we showcase our signature ring and why gold is timeless...',
            'image'       => 'https://images.pexels.com/photos/38404053/pexels-photo-38404053.jpeg',
            'category'    => 'Education',
            'date'        => now(),
            'product_id'  => $product ? $product->id : null,
        ]);

        Blog::create([
            'title'       => 'A Buyer\'s Guide to Diamonds',
            'description' => 'Learn what to look for in a quality diamond, choosing the ideal cut, clarity, color, and carat.',
            'image'       => 'https://images.pexels.com/photos/6522302/pexels-photo-6522302.jpeg',
            'category'    => 'Guide',
            'date'        => now()->subDays(1),
            'product_id'  => $product ? $product->id : null,
        ]);

        Blog::create([
            'title'       => 'Top 5 Jewelry Trends in 2024',
            'description' => 'From modern minimalism to vintage flair, discover the top jewelry trends shaping 2024.',
            'image'       => 'https://images.pexels.com/photos/9651419/pexels-photo-9651419.jpeg',
            'category'    => 'Trends',
            'date'        => now()->subDays(2),
            'product_id'  => $product ? $product->id : null,
        ]);

        Blog::create([
            'title'       => 'Caring For Your Fine Jewelry',
            'description' => 'Keep your jewelry shining with these expert care, cleaning, and maintenance tips.',
            'image'       => 'https://images.pexels.com/photos/1670723/pexels-photo-1670723.jpeg',
            'category'    => 'Care Tips',
            'date'        => now()->subDays(3),
            'product_id'  => $product ? $product->id : null,
        ]);

        Blog::create([
            'title'       => 'The Symbolism of Gemstones',
            'description' => 'Explore the fascinating meanings and histories behind popular gemstones used in luxury jewelry.',
            'image'       => 'https://images.pexels.com/photos/9080092/pexels-photo-9080092.jpeg',
            'category'    => 'Education',
            'date'        => now()->subDays(4),
            'product_id'  => $product ? $product->id : null,
        ]);

        Blog::create([
            'title'       => 'How to Style Statement Pieces',
            'description' => 'Tips on making a statement with bold rings, necklaces, and bracelets for every occasion.',
            'image'       => 'https://images.pexels.com/photos/18016512/pexels-photo-18016512.jpeg',
            'category'    => 'Style',
            'date'        => now()->subDays(5),
            'product_id'  => $product ? $product->id : null,
        ]);
        Blog::create([
            'title'       => 'How to Style Statement Pieces',
            'description' => 'Tips on making a statement with bold rings, necklaces, and bracelets for every occasion.',
            'image'       => 'https://images.pexels.com/photos/30541174/pexels-photo-30541174.jpeg',
            'category'    => 'Style',
            'date'        => now()->subDays(5),
            'product_id'  => $product ? $product->id : null,
        ]);
        Blog::create([
            'title'       => 'How to Style Statement Pieces',
            'description' => 'Tips on making a statement with bold rings, necklaces, and bracelets for every occasion.',
            'image'       => 'https://images.pexels.com/photos/12427694/pexels-photo-12427694.jpeg',
            'category'    => 'Style',
            'date'        => now()->subDays(5),
            'product_id'  => $product ? $product->id : null,
        ]);
        Blog::create([
            'title'       => 'How to Style Statement Pieces',
            'description' => 'Tips on making a statement with bold rings, necklaces, and bracelets for every occasion.',
            'image'       => 'https://images.pexels.com/photos/30541186/pexels-photo-30541186.jpeg',
            'category'    => 'Style',
            'date'        => now()->subDays(5),
            'product_id'  => $product ? $product->id : null,
        ]);
   
    }
}