<?php

namespace App\Http\Controllers;

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
     * Add a new pet to the store
     *
     * @param StorePetRequest $request Request containing validated pet data
     * @return JsonResponse JSON response with created pet data or error
     */
    public function store(StorePetRequest $request): JsonResponse
    {
        try {
            $pet = $this->petService->createPet($request->validated());

            return response()->json(new PetResource($pet), 200);
        } catch (\Exception $e) {
            Log::error('Error creating pet', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'request' => $request->validated()
            ]);
            return response()->json([
                'message' => 'Wystąpił błąd podczas dodawania nowego zwierzaka'
            ], 405);
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

    /**
     * Update an existing pet
     *
     * @param UpdatePetRequest $request Request containing validated pet data with ID
     * @return JsonResponse JSON response with updated pet data or error
     */
    public function update(UpdatePetRequest $request): JsonResponse
    {
        try {
            $pet = $this->petService->updatePet($request->validated());

            return response()->json(new PetResource($pet), 200);
        } catch (\Exception $e) {
            Log::error('Error updating pet', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'request' => $request->validated()
            ]);

            if ($e->getCode() === 404) {
                return response()->json([
                    'message' => 'Pet not found'
                ], 404);
            }

            return response()->json([
                'message' => 'Wystąpił błąd podczas aktualizacji zwierzaka'
            ], 405);
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
        try {
            $this->petService->deletePet($petId);

            return response()->json(null, 200);
        } catch (\Exception $e) {
            Log::error('Error deleting pet', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'pet_id' => $petId
            ]);

            if ($e->getCode() === 404) {
                return response()->json([
                    'message' => 'Pet not found'
                ], 404);
            }

            return response()->json([
                'message' => 'Wystąpił błąd podczas usuwania zwierzaka'
            ], 500);
        }
    }
}
