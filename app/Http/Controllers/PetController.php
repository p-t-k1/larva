<?php

namespace App\Http\Controllers;

use App\Services\PetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

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
    public function findByStatus(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'status' => 'required|array',
                'status.*' => ['string', Rule::in(['available', 'pending', 'sold'])],
            ]);

            $statusArray = $validated['status'];

            $pets = $this->petService->findByStatus($statusArray);

            return response()->json($pets, 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation failed for pets search', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);
            return response()->json([
                'message' => 'Podano nieprawidłowy status zwierzęcia. Dozwolone opcje to: available (dostępny), pending (w trakcie adopcji), sold (adoptowany)'
            ], 400);
        } catch (\Exception $e) {
            Log::error('Error fetching pets', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'request' => $request->all()
            ]);
            return response()->json([
                'message' => 'Wystąpił błąd podczas pobierania danych'
            ], $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500);
        }
    }
}
