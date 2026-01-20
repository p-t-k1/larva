<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PetController;

Route::get('/pets/findByStatus', [PetController::class, 'findByStatus'])->name('pets.findByStatus');
