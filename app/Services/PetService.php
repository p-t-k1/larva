<?php

namespace App\Services;

use App\Models\Pet;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\QueryException;
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
}
