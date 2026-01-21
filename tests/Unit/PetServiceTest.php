<?php

namespace Tests\Unit;

use App\Enums\PetStatus;
use App\Models\Category;
use App\Models\Pet;
use App\Models\Tag;
use App\Services\PetService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PetService $petService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->petService = new PetService();
    }

    public function test_create_pet_creates_category_when_not_exists(): void
    {
        $this->assertDatabaseMissing('categories', ['name' => 'Dogs']);

        $data = [
            'name' => 'Buddy',
            'category' => ['id' => 999, 'name' => 'Dogs'],
        ];

        $result = $this->petService->createPet($data);

        $this->assertDatabaseHas('categories', ['name' => 'Dogs']);
        $this->assertEquals('Dogs', $result->category->name);
    }

    public function test_create_pet_reuses_existing_category(): void
    {
        $existingCategory = Category::factory()->create(['name' => 'Dogs']);

        $data = [
            'name' => 'Buddy',
            'category' => ['id' => 999, 'name' => 'Dogs'],
        ];

        $result = $this->petService->createPet($data);

        $this->assertEquals($existingCategory->id, $result->category_id);
        $this->assertEquals(1, Category::where('name', 'Dogs')->count());
    }

    public function test_create_pet_creates_and_syncs_multiple_tags(): void
    {
        $this->assertDatabaseMissing('tags', ['name' => 'friendly']);
        $this->assertDatabaseMissing('tags', ['name' => 'cute']);

        $data = [
            'name' => 'Buddy',
            'tags' => [
                ['id' => 1, 'name' => 'friendly'],
                ['id' => 2, 'name' => 'cute'],
            ],
        ];

        $result = $this->petService->createPet($data);

        $this->assertDatabaseHas('tags', ['name' => 'friendly']);
        $this->assertDatabaseHas('tags', ['name' => 'cute']);
        $this->assertCount(2, $result->tags);
        $this->assertTrue($result->tags->pluck('name')->contains('friendly'));
        $this->assertTrue($result->tags->pluck('name')->contains('cute'));
    }

    public function test_create_pet_applies_default_status(): void
    {
        $data = ['name' => 'Buddy'];

        $result = $this->petService->createPet($data);

        $this->assertEquals(PetStatus::AVAILABLE->value, $result->status);
    }

    public function test_update_pet_replaces_tags_completely(): void
    {
        $category = Category::factory()->create();
        $oldTag = Tag::factory()->create(['name' => 'old_tag']);
        $pet = Pet::factory()->create(['category_id' => $category->id]);
        $pet->tags()->attach($oldTag);

        $this->assertDatabaseHas('pet_tag', [
            'pet_id' => $pet->id,
            'tag_id' => $oldTag->id,
        ]);

        $data = [
            'id' => $pet->id,
            'name' => 'Updated',
            'tags' => [['id' => 999, 'name' => 'new_tag']],
        ];

        $result = $this->petService->updatePet($data);

        $this->assertDatabaseMissing('pet_tag', [
            'pet_id' => $pet->id,
            'tag_id' => $oldTag->id,
        ]);
        $this->assertCount(1, $result->tags);
        $this->assertEquals('new_tag', $result->tags->first()->name);
    }

    public function test_delete_pet_removes_tag_associations(): void
    {
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();
        $pet = Pet::factory()->create(['category_id' => $category->id]);
        $pet->tags()->attach($tag);

        $this->assertDatabaseHas('pet_tag', [
            'pet_id' => $pet->id,
            'tag_id' => $tag->id,
        ]);

        $this->petService->deletePet($pet->id);

        $this->assertDatabaseMissing('pet_tag', ['pet_id' => $pet->id]);
    }

    public function test_delete_pet_throws_exception_when_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->petService->deletePet(99999);
    }
}
