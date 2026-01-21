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

    public function test_can_create_pet_with_full_data(): void
    {
        $petData = [
            'name' => 'doggie',
            'status' => 'available',
            'photoUrls' => ['https://example.com/photo1.jpg', 'https://example.com/photo2.jpg'],
            'category' => [
                'id' => 1,
                'name' => 'Dogs'
            ],
            'tags' => [
                ['id' => 1, 'name' => 'friendly'],
                ['id' => 2, 'name' => 'cute']
            ]
        ];

        $response = $this->postJson('/api/pet', $petData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id', 'name', 'status', 'photo_urls', 'category', 'tags', 'created_at', 'updated_at'
            ])
            ->assertJsonPath('name', 'doggie')
            ->assertJsonPath('status', 'available')
            ->assertJsonPath('category.name', 'Dogs')
            ->assertJsonCount(2, 'tags')
            ->assertJsonPath('photo_urls', ['https://example.com/photo1.jpg', 'https://example.com/photo2.jpg']);

        $this->assertDatabaseHas('pets', [
            'name' => 'doggie',
            'status' => 'available'
        ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Dogs'
        ]);

        $this->assertDatabaseHas('tags', [
            'name' => 'friendly'
        ]);

        $this->assertDatabaseHas('tags', [
            'name' => 'cute'
        ]);
    }

    public function test_can_create_pet_with_minimal_data(): void
    {
        $petData = [
            'name' => 'Fluffy'
        ];

        $response = $this->postJson('/api/pet', $petData);

        $response->assertStatus(200)
            ->assertJsonPath('name', 'Fluffy')
            ->assertJsonPath('status', 'available')
            ->assertJsonPath('photo_urls', []);

        $this->assertDatabaseHas('pets', [
            'name' => 'Fluffy',
            'status' => 'available',
            'category_id' => null
        ]);
    }

    public function test_validation_fails_when_name_is_missing(): void
    {
        $petData = [
            'status' => 'available'
        ];

        $response = $this->postJson('/api/pet', $petData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_validation_fails_with_invalid_status_on_create(): void
    {
        $petData = [
            'name' => 'Buddy',
            'status' => 'invalid_status'
        ];

        $response = $this->postJson('/api/pet', $petData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    public function test_validation_fails_with_invalid_photo_url(): void
    {
        $petData = [
            'name' => 'Buddy',
            'photoUrls' => ['not-a-valid-url']
        ];

        $response = $this->postJson('/api/pet', $petData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['photoUrls.0']);
    }

    public function test_creates_or_uses_existing_category(): void
    {
        $existingCategory = Category::factory()->create(['name' => 'Dogs']);

        $petData = [
            'name' => 'Rex',
            'category' => [
                'id' => 99,
                'name' => 'Dogs'
            ]
        ];

        $response = $this->postJson('/api/pet', $petData);

        $response->assertStatus(200)
            ->assertJsonPath('category.id', $existingCategory->id)
            ->assertJsonPath('category.name', 'Dogs');

        $this->assertEquals(1, Category::where('name', 'Dogs')->count());
    }

    public function test_creates_or_uses_existing_tags(): void
    {
        $existingTag = Tag::factory()->create(['name' => 'friendly']);

        $petData = [
            'name' => 'Max',
            'tags' => [
                ['id' => 99, 'name' => 'friendly'],
                ['id' => 100, 'name' => 'playful']
            ]
        ];

        $response = $this->postJson('/api/pet', $petData);

        $response->assertStatus(200)
            ->assertJsonCount(2, 'tags');

        $this->assertEquals(1, Tag::where('name', 'friendly')->count());
        $this->assertEquals(1, Tag::where('name', 'playful')->count());
    }

    public function test_can_delete_existing_pet(): void
    {
        $category = Category::factory()->create();
        $pet = Pet::factory()->create(['category_id' => $category->id]);

        $response = $this->deleteJson("/api/pet/{$pet->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('pets', [
            'id' => $pet->id
        ]);
    }

    public function test_delete_returns_404_for_non_existent_pet(): void
    {
        $response = $this->deleteJson('/api/pet/99999');

        $response->assertStatus(404)
            ->assertJsonStructure(['message']);
    }

    public function test_delete_removes_pet_tag_associations(): void
    {
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();
        $pet = Pet::factory()->create(['category_id' => $category->id]);
        $pet->tags()->attach($tag);

        $this->assertDatabaseHas('pet_tag', [
            'pet_id' => $pet->id,
            'tag_id' => $tag->id
        ]);

        $response = $this->deleteJson("/api/pet/{$pet->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('pet_tag', [
            'pet_id' => $pet->id
        ]);
    }

    public function test_delete_with_invalid_id_format_returns_404(): void
    {
        $response = $this->deleteJson('/api/pet/0');

        $response->assertStatus(404);
    }
}
