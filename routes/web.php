<?php

use App\Http\Controllers\PetController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pets.index');
})->name('pets.index');

Route::get('/pets/create', [PetController::class, 'create'])->name('pets.create');
