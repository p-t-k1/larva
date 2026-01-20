<?php

namespace App\Services;

use App\Models\Pet;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
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
            throw new Exception('Database error occurred while fetching pets', 500, $e);
        }
    }
}
