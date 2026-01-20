<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        Tag::create(['name' => 'friendly']);
        Tag::create(['name' => 'playful']);
        Tag::create(['name' => 'young']);
        Tag::create(['name' => 'trained']);
        Tag::create(['name' => 'vaccinated']);
    }
}
