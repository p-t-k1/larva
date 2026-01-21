<?php

namespace App\Services;

use App\Enums\PetStatus;
use App\Models\Pet;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PetService
{
    /**
     * Find pets by status with pagination
     *
     * Retrieves pets filtered by status array with their related category and tags.
     * Results are paginated for performance.
     *
     * @param array $statusArray Array of status values to filter by (available, pending, sold)
     * @param int $perPage Number of items per page (default: 15)
     * @return LengthAwarePaginator Paginated collection of Pet models
     * @throws Exception When database query fails
     */
    public function findByStatus(array $statusArray, int $perPage = 15)
    {
        return Pet::with(['category', 'tags'])
            ->whereIn('status', $statusArray)
            ->paginate($perPage);
    }

    /**
     * Create a new pet
     *
     * @param array $data Validated pet data
     * @return Pet Created pet model with relations loaded
     * @throws Exception When database operation fails
     */
    public function createPet(array $data): Pet
    {
        return DB::transaction(function () use ($data) {
            $categoryId = null;
            
            if (isset($data['category'])) {
                $category = Category::firstOrCreate(
                    ['name' => $data['category']['name']],
                    ['name' => $data['category']['name']]
                );
                $categoryId = $category->id;
            }

            $pet = Pet::create([
                'name' => $data['name'],
                'category_id' => $categoryId,
                'photo_urls' => $data['photoUrls'] ?? [],
                'status' => $data['status'] ?? PetStatus::AVAILABLE->value,
            ]);

            if (isset($data['tags']) && is_array($data['tags'])) {
                $tagIds = [];
                foreach ($data['tags'] as $tagData) {
                    $tag = Tag::firstOrCreate(
                        ['name' => $tagData['name']],
                        ['name' => $tagData['name']]
                    );
                    $tagIds[] = $tag->id;
                }
                $pet->tags()->sync($tagIds);
            }

            $pet->load(['category', 'tags']);

            return $pet;
        });
    }

    /**
     * Update an existing pet
     *
     * @param array $data Validated pet data including ID
     * @return Pet Updated pet model with relations loaded
     * @throws Exception When pet not found or database operation fails
     */
    public function updatePet(array $data): Pet
    {
        return DB::transaction(function () use ($data) {
            $pet = Pet::findOrFail($data['id']);

            $categoryId = $pet->category_id;
            
            if (isset($data['category'])) {
                $category = Category::firstOrCreate(
                    ['name' => $data['category']['name']],
                    ['name' => $data['category']['name']]
                );
                $categoryId = $category->id;
            }

            $pet->update([
                'name' => $data['name'],
                'category_id' => $categoryId,
                'photo_urls' => $data['photoUrls'] ?? $pet->photo_urls,
                'status' => $data['status'] ?? $pet->status,
            ]);

            if (isset($data['tags']) && is_array($data['tags'])) {
                $tagIds = [];
                foreach ($data['tags'] as $tagData) {
                    $tag = Tag::firstOrCreate(
                        ['name' => $tagData['name']],
                        ['name' => $tagData['name']]
                    );
                    $tagIds[] = $tag->id;
                }
                $pet->tags()->sync($tagIds);
            }

            $pet->load(['category', 'tags']);

            return $pet;
        });
    }

    /**
     * Delete a pet by ID
     *
     * @param int $petId Pet ID to delete
     * @return bool True if pet was deleted
     * @throws Exception When pet not found or database operation fails
     */
    public function deletePet(int $petId): bool
    {
        $pet = Pet::findOrFail($petId);

        return DB::transaction(function () use ($pet) {
            $pet->tags()->detach();
            return $pet->delete();
        });
    }
}
