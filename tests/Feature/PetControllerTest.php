<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Pet;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_pets_by_status(): void
    {
        $category = Category::factory()->create(['name' => 'Dogs']);
        $tag = Tag::factory()->create(['name' => 'friendly']);

        $pet = Pet::factory()->create([
            'category_id' => $category->id,
            'status' => 'available',
            'name' => 'Buddy'
        ]);
        $pet->tags()->attach($tag);

        $response = $this->getJson('/api/pets/findByStatus?status[]=available');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'status', 'photo_urls', 'category', 'tags']
                ],
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
                'links' => ['first', 'last', 'prev', 'next']
            ])
            ->assertJsonPath('data.0.name', 'Buddy')
            ->assertJsonPath('data.0.status', 'available');
    }

    public function test_returns_empty_data_when_no_pets_found(): void
    {
        $response = $this->getJson('/api/pets/findByStatus?status[]=available');

        $response->assertStatus(200)
            ->assertJsonPath('data', [])
            ->assertJsonPath('meta.total', 0);
    }

    public function test_validation_fails_with_invalid_status(): void
    {
        $response = $this->getJson('/api/pets/findByStatus?status[]=invalid');

        $response->assertStatus(422);
    }

    public function test_validation_fails_without_status_parameter(): void
    {
        $response = $this->getJson('/api/pets/findByStatus');

        $response->assertStatus(422);
    }

    public function test_pagination_works_correctly(): void
    {
        $category = Category::factory()->create();
        Pet::factory()->count(25)->create([
            'category_id' => $category->id,
            'status' => 'available'
        ]);

        $response = $this->getJson('/api/pets/findByStatus?status[]=available&per_page=10');

        $response->assertStatus(200)
            ->assertJsonPath('meta.per_page', 10)
            ->assertJsonPath('meta.total', 25)
            ->assertJsonCount(10, 'data');
    }

    public function test_filters_pets_by_multiple_statuses(): void
    {
        $category = Category::factory()->create();
        Pet::factory()->create(['category_id' => $category->id, 'status' => 'available']);
        Pet::factory()->create(['category_id' => $category->id, 'status' => 'pending']);
        Pet::factory()->create(['category_id' => $category->id, 'status' => 'sold']);

        $response = $this->getJson('/api/pets/findByStatus?status[]=available&status[]=pending');

        $response->assertStatus(200)
            ->assertJsonPath('meta.total', 2);
    }
}
