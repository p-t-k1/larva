<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Pet;
use Illuminate\Database\Eloquent\Factories\Factory;

class PetFactory extends Factory
{
    protected $model = Pet::class;

    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => $this->faker->name(),
            'photo_urls' => [$this->faker->imageUrl()],
            'status' => $this->faker->randomElement(['available', 'pending', 'sold']),
        ];
    }
}
