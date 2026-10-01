<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $ringCategory = Category::where('name', 'Rings')->first();
        $necklaceCategory = Category::where('name', 'Necklaces')->first();
        $braceletCategory = Category::where('name', 'Bracelets')->first();

        $names_rings = [
            'Royal Gem Ring', 'Opulent Halo Ring', 'Sunset Ruby Ring', 'Twilight Opal Ring',
            'Golden Splendor Ring', 'Moonstone Wisp Ring', 'Radiant Heart Ring', 'Crown Jewel Ring',
            'Infinity Loop Ring', 'Celestial Diamond Ring', 'Vintage Glow Ring', 'Baroque Beauty Ring'
        ];
        
        $names_necklaces = [
            'Queen\'s Grace Necklace', 'Starlit Charm Necklace', 'Elegant Pearl Necklace', 'Dawn Sapphire Necklace',
            'Aurora Pendant Necklace', 'Trinity Knot Necklace', 'Golden Bar Necklace', 'Serene Bloom Necklace',
            'Lustre Teardrop Necklace', 'Moonbeam Chain Necklace', 'Regal Crest Necklace', 'Emerald Locket Necklace'
        ];

        $names_bracelets = [
            'Classic Pearl Bracelet', 'Twist of Gold Bracelet', 'Silver Cuff Bracelet', 'Opal Chain Bracelet',
            'Sleek Infinity Bracelet', 'Shimmer Bangle Bracelet', 'Chic Link Bracelet', 'Rose Gold Knot Bracelet',
            'Bold Loop Bracelet', 'Graceful Wave Bracelet', 'Black Onyx Bracelet', 'Starlight Charm Bracelet'
        ];

        $desc_templates = [
            'A beautiful %s jewelry piece, handcrafted with quality stones and precious metals.',
            'Timeless elegance and modern design unite in this %s, ideal for celebrations or daily style.',
            'Exquisitely detailed and designed for glamour, this %s truly stands out from the rest.',
            'This %s blends classic tradition with contemporary chic for a truly unique statement.'
        ];

        $ring_images = [
            'https://images.pexels.com/photos/19279699/pexels-photo-19279699.jpeg', 
            'https://images.pexels.com/photos/34372549/pexels-photo-34372549.jpeg',
            'https://images.pexels.com/photos/21928764/pexels-photo-21928764.jpeg', 
            'https://images.pexels.com/photos/34372563/pexels-photo-34372563.jpeg', 
            'https://images.pexels.com/photos/30232951/pexels-photo-30232951.jpeg', 
            'https://images.pexels.com/photos/37488823/pexels-photo-37488823.jpeg', 
        ];

        $necklace_images = [
            'https://images.pexels.com/photos/34372552/pexels-photo-34372552.jpeg', 
            'https://images.pexels.com/photos/34372552/pexels-photo-34372552.jpeg', 
            'https://images.pexels.com/photos/34372552/pexels-photo-34372552.jpeg', 
            'https://images.pexels.com/photos/34372552/pexels-photo-34372552.jpeg', 
            'https://images.pexels.com/photos/34372552/pexels-photo-34372552.jpeg', 
            'https://images.pexels.com/photos/34372552/pexels-photo-34372552.jpeg',  
        ];

        $bracelet_images = [
            'https://images.pexels.com/photos/34399143/pexels-photo-34399143.jpeg',
            'https://images.pexels.com/photos/34399143/pexels-photo-34399143.jpeg',
            'https://images.pexels.com/photos/34399143/pexels-photo-34399143.jpeg',
            'https://images.pexels.com/photos/34399143/pexels-photo-34399143.jpeg',
            'https://images.pexels.com/photos/34399143/pexels-photo-34399143.jpeg', 
        ];

        $jewelry_videos = [
            asset('storage/products/videos/vedio.mp4'), 
        ];
        
        $productsToCreate = 30;
        $categories = [
            [
                'model' => $ringCategory,
                'names' => $names_rings,
                'images' => $ring_images
            ],
            [
                'model' => $necklaceCategory,
                'names' => $names_necklaces,
                'images' => $necklace_images
            ],
            [
                'model' => $braceletCategory,
                'names' => $names_bracelets,
                'images' => $bracelet_images
            ]
        ];

        for ($i = 1; $i <= $productsToCreate; $i++) {
            // Pick category
            $catIndex = ($i - 1) % count($categories);
            $category = $categories[$catIndex]['model'];
            $nameArr  = $categories[$catIndex]['names'];
            $imageArr = $categories[$catIndex]['images'];

            // Random name from category
            $baseName = $nameArr[array_rand($nameArr)];
            $name = $baseName . ' ' . Str::random(3) . $i;
            $slug = Str::slug($baseName) . '-' . $i;
            $description = sprintf(
                $desc_templates[array_rand($desc_templates)],
                strtolower($baseName)
            );
            $price = rand(800, 5000) + (rand(10, 99) / 100); // $800.10 - $5000.99
            $image = $imageArr[array_rand($imageArr)];
            $video = $jewelry_videos[array_rand($jewelry_videos)];
            $stock = rand(3, 30);

            Product::create([
                'category_id' => $category->id,
                'name'        => $name,
                'slug'        => $slug,
                'description' => $description,
                'price'       => $price,
                'image'       => $image,
                'video'       => $video,
                'stock'       => $stock
            ]);
        }
    }
}