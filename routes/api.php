<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PetController;

Route::get('/pet/findByStatus', [PetController::class, 'findByStatus'])->name('pet.findByStatus');
