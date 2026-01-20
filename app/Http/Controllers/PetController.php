<?php

namespace App\Http\Controllers;

use App\Http\Requests\FindPetsByStatusRequest;
use App\Http\Resources\PetResource;
use App\Services\PetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class PetController extends Controller
{
    public function __construct(
        private PetService $petService
    ) {}

    /**
     * Finds Pets by status
     *
     * Multiple status values can be provided with comma separated strings
     * Available values : available, pending, sold
     */
    public function findByStatus(FindPetsByStatusRequest $request): JsonResponse
    {
        try {
            $statusArray = $request->validated()['status'];
            $perPage = (int) $request->input('per_page', 15);

            $pets = $this->petService->findByStatus($statusArray, $perPage);

            return response()->json([
                'data' => PetResource::collection($pets->items()),
                'meta' => [
                    'current_page' => $pets->currentPage(),
                    'last_page' => $pets->lastPage(),
                    'per_page' => $pets->perPage(),
                    'total' => $pets->total(),
                    'from' => $pets->firstItem(),
                    'to' => $pets->lastItem(),
                ],
                'links' => [
                    'first' => $pets->url(1),
                    'last' => $pets->url($pets->lastPage()),
                    'prev' => $pets->previousPageUrl(),
                    'next' => $pets->nextPageUrl(),
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching pets', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'request' => $request->validated()
            ]);
            return response()->json([
                'message' => 'Wystąpił błąd podczas pobierania danych'
            ], $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500);
        }
    }
}
