<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PetController extends Controller
{
    /**
     * Finds Pets by status
     *
     * Multiple status values can be provided with comma separated strings
     * Available values : available, pending, sold
     */
    public function findByStatus(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|array',
            'status.*' => ['string', Rule::in(['available', 'pending', 'sold'])],
        ]);

        $statusArray = $validated['status'];

        $pets = Pet::with(['category', 'tags'])
            ->whereIn('status', $statusArray)
            ->get();

        return response()->json($pets, 200);
    }
}
