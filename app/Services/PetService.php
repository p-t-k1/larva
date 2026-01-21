<?php

namespace App\Services;

use App\Models\Pet;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

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
        try {
            return Pet::with(['category', 'tags'])
                ->whereIn('status', $statusArray)
                ->paginate($perPage);
        } catch (QueryException $e) {
            Log::error('Database error while fetching pets', [
                'status_array' => $statusArray,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw new Exception('Database error occurred while fetching pets', 500, $e);
        }
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
        try {
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
                    'status' => $data['status'] ?? 'available',
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
        } catch (QueryException $e) {
            Log::error('Database error while creating pet', [
                'data' => $data,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw new Exception('Database error occurred while creating pet', 500, $e);
        }
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
        try {
            $pet = Pet::find($petId);

            if (!$pet) {
                throw new Exception('Pet not found', 404);
            }

            return DB::transaction(function () use ($pet) {
                $pet->tags()->detach();
                return $pet->delete();
            });
        } catch (QueryException $e) {
            Log::error('Database error while deleting pet', [
                'pet_id' => $petId,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw new Exception('Database error occurred while deleting pet', 500, $e);
        }
    }
}
