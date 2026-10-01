<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Rings'],
            ['name' => 'Necklaces'],
            ['name' => 'Bracelets'],
            ['name' => 'Earrings'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate($category);
        }
    }
}