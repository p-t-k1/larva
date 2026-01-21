<?php

namespace App\Http\Controllers;

use App\Enums\PetStatus;
use App\Http\Requests\FindPetsByStatusRequest;
use App\Http\Requests\StorePetRequest;
use App\Http\Requests\UpdatePetRequest;
use App\Http\Resources\PetResource;
use App\Services\PetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class PetController extends Controller
{
    /**
     * @param PetService $petService
     */
    public function __construct(
        private PetService $petService
    ) {}

    /**
     * Show the form for creating a new pet
     */
    public function create()
    {
        return view('pets.create');
    }

    /**
     * Get pet configuration options
     *
     * @return JsonResponse JSON response with statuses, categories and tags
     */
    public function getConfig(): JsonResponse
    {
        return response()->json([
            'statuses' => PetStatus::toArray(),
            'categories' => [
                ['value' => 'Dogs', 'label' => 'Psy'],
                ['value' => 'Cats', 'label' => 'Koty'],
                ['value' => 'Birds', 'label' => 'Ptaki'],
                ['value' => 'Fish', 'label' => 'Ryby'],
                ['value' => 'Rabbits', 'label' => 'Króliki'],
            ],
            'tags' => [
                ['value' => 'friendly', 'label' => 'Przyjazny'],
                ['value' => 'playful', 'label' => 'Zabawny'],
                ['value' => 'young', 'label' => 'Młody'],
                ['value' => 'trained', 'label' => 'Wyszkolony'],
                ['value' => 'vaccinated', 'label' => 'Zaszczepiony'],
            ],
        ], 200);
    }

    /**
     * Add a new pet to the store
     *
     * @param StorePetRequest $request Request containing validated pet data
     * @return JsonResponse JSON response with created pet data or error
     */
    public function store(StorePetRequest $request): JsonResponse
    {
        $startTime = microtime(true);
        try {
            $pet = $this->petService->createPet($request->validated());

            return response()->json(new PetResource($pet), 200);
        } catch (\Exception $e) {
            $duration = round((microtime(true) - $startTime) * 1000, 2);
            Log::error('Error creating pet', [
                'method' => $request->method(),
                'status' => 500,
                'duration_ms' => $duration,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Wystąpił błąd podczas dodawania nowego zwierzaka'
            ], 500);
        }
    }

    /**
     * Find pets by status
     *
     * Retrieves a paginated list of pets filtered by one or more status values.
     * Multiple status values can be provided as an array.
     *
     * @param FindPetsByStatusRequest $request Request containing validated status array
     * @return JsonResponse JSON response with paginated pet data, metadata and navigation links
     * @throws \Exception When database error occurs
     */
    public function findByStatus(FindPetsByStatusRequest $request): JsonResponse
    {
        $startTime = microtime(true);
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
            $duration = round((microtime(true) - $startTime) * 1000, 2);
            Log::error('Error fetching pets', [
                'method' => $request->method(),
                'status' => 500,
                'duration_ms' => $duration,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Wystąpił błąd podczas pobierania danych'
            ], 500);
        }
    }

    /**
     * Update an existing pet
     *
     * @param UpdatePetRequest $request Request containing validated pet data with ID
     * @return JsonResponse JSON response with updated pet data or error
     */
    public function update(UpdatePetRequest $request): JsonResponse
    {
        $startTime = microtime(true);
        try {
            $pet = $this->petService->updatePet($request->validated());

            return response()->json(new PetResource($pet), 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $duration = round((microtime(true) - $startTime) * 1000, 2);
            Log::warning('Pet not found during update', [
                'method' => $request->method(),
                'status' => 404,
                'duration_ms' => $duration,
                'pet_id' => $request->validated()['id'],
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
            return response()->json([
                'message' => 'Nie znaleziono takiego zwierzaka'
            ], 404);
        } catch (\Exception $e) {
            $duration = round((microtime(true) - $startTime) * 1000, 2);
            Log::error('Error updating pet', [
                'method' => $request->method(),
                'status' => 500,
                'duration_ms' => $duration,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Wystąpił błąd podczas aktualizacji zwierzaka'
            ], 500);
        }
    }

    /**
     * Delete a pet
     *
     * @param int $petId Pet ID to delete
     * @return JsonResponse Empty response on success or error message
     */
    public function destroy(int $petId): JsonResponse
    {
        $startTime = microtime(true);
        try {
            $this->petService->deletePet($petId);

            return response()->json(null, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $duration = round((microtime(true) - $startTime) * 1000, 2);
            Log::warning('Pet not found during delete', [
                'method' => request()->method(),
                'status' => 404,
                'duration_ms' => $duration,
                'pet_id' => $petId,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
            return response()->json([
                'message' => 'Nie znaleziono takiego zwierzaka'
            ], 404);
        } catch (\Exception $e) {
            $duration = round((microtime(true) - $startTime) * 1000, 2);
            Log::error('Error deleting pet', [
                'method' => request()->method(),
                'status' => 500,
                'duration_ms' => $duration,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Wystąpił błąd podczas usuwania zwierzaka'
            ], 500);
        }
    }
}
