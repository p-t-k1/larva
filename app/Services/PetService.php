<?php

namespace App\Services;

use App\Models\Pet;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Exception;

class PetService
{
    public function findByStatus(array $statusArray): Collection
    {
        try {
            return Pet::with(['category', 'tags'])
                ->whereIn('status', $statusArray)
                ->get();
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
