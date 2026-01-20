<?php

namespace Database\Seeders;

use App\Models\Pet;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class PetSeeder extends Seeder
{
    public function run(): void
    {
        $dogCategory = Category::where('name', 'Dogs')->first();
        $catCategory = Category::where('name', 'Cats')->first();
        $birdCategory = Category::where('name', 'Birds')->first();

        $friendly = Tag::where('name', 'friendly')->first();
        $playful = Tag::where('name', 'playful')->first();
        $young = Tag::where('name', 'young')->first();
        $trained = Tag::where('name', 'trained')->first();

        Pet::create([
            'name' => 'Burek',
            'category_id' => $dogCategory->id,
            'photo_urls' => ['https://images.unsplash.com/photo-1552053831-71594a27632d?auto=format&fit=crop&w=600'],
            'status' => 'available',
        ])->tags()->attach([$friendly->id, $playful->id, $trained->id]);

        Pet::create([
            'name' => 'Max',
            'category_id' => $dogCategory->id,
            'photo_urls' => ['https://images.unsplash.com/photo-1543466835-00a7907e9de1?auto=format&fit=crop&w=600'],
            'status' => 'available',
        ])->tags()->attach([$young->id, $playful->id]);

        Pet::create([
            'name' => 'Luna',
            'category_id' => $catCategory->id,
            'photo_urls' => ['https://images.unsplash.com/photo-1574158622682-e40e69881006?auto=format&fit=crop&w=600'],
            'status' => 'pending',
        ])->tags()->attach([$friendly->id]);

        Pet::create([
            'name' => 'Mruczek',
            'category_id' => $catCategory->id,
            'photo_urls' => ['https://images.unsplash.com/photo-1519052537078-e6302a4968d4?auto=format&fit=crop&w=600'],
            'status' => 'available',
        ])->tags()->attach([$young->id, $playful->id]);

        Pet::create([
            'name' => 'Ćwirek',
            'category_id' => $birdCategory->id,
            'photo_urls' => ['https://images.unsplash.com/photo-1552728089-57bdde30beb3?auto=format&fit=crop&w=600'],
            'status' => 'available',
        ])->tags()->attach([$friendly->id, $trained->id]);

        Pet::create([
            'name' => 'Reksio',
            'category_id' => $dogCategory->id,
            'photo_urls' => ['https://images.unsplash.com/photo-1558788353-f76d92427f16?auto=format&fit=crop&w=600'],
            'status' => 'sold',
        ])->tags()->attach([$friendly->id, $trained->id]);

        Pet::create([
            'name' => 'Puszek',
            'category_id' => $catCategory->id,
            'photo_urls' => ['https://images.unsplash.com/photo-1513360371669-4adf3dd7dff8?auto=format&fit=crop&w=600'],
            'status' => 'available',
        ])->tags()->attach([$young->id]);
    }
}
