<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PetController;

Route::middleware(['throttle:api'])->group(function () {
    Route::post('/pet', [PetController::class, 'store'])->name('pet.store');
    Route::get('/pets/findByStatus', [PetController::class, 'findByStatus'])->name('pets.findByStatus');
    Route::delete('/pet/{petId}', [PetController::class, 'destroy'])->name('pet.destroy');
});
