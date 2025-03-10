<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Jardinage', 'slug' => 'gardening'],
            ['name' => 'Bricolage', 'slug' => 'diy'],
            ['name' => 'Cuisine', 'slug' => 'cooking'],
            ['name' => 'Décoration', 'slug' => 'decoration'],
            ['name' => 'Électronique', 'slug' => 'electronics'],
            ['name' => 'Informatique', 'slug' => 'computers'],
            ['name' => 'Jeux', 'slug' => 'games'],
            ['name' => 'Sport', 'slug' => 'sports'],
            ['name' => 'Loisirs créatifs', 'slug' => 'crafts'],
            ['name' => 'Vêtements', 'slug' => 'clothing'],
            ['name' => 'Accessoires', 'slug' => 'accessories'],
            ];

        foreach ($categories as $category) {
            \App\Models\Category::create($category);
        }
    }
}
