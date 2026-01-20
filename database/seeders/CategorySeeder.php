<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::create(['name' => 'Dogs']);
        Category::create(['name' => 'Cats']);
        Category::create(['name' => 'Birds']);
        Category::create(['name' => 'Fish']);
        Category::create(['name' => 'Rabbits']);
    }
}
