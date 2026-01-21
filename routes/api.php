<?php

use App\Http\Controllers\PetController;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:api'])->group(function () {
    Route::get('/config/pet-options', [PetController::class, 'getConfig'])->name('pet.config');
    Route::post('/pet', [PetController::class, 'store'])->name('pet.store');
    Route::put('/pet', [PetController::class, 'update'])->name('pet.update');
    Route::get('/pets/findByStatus', [PetController::class, 'findByStatus'])->name('pets.findByStatus');
    Route::delete('/pet/{petId}', [PetController::class, 'destroy'])->name('pet.destroy');
});
