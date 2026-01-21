<?php

use App\Http\Controllers\PetController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $status = request('status', 'available');
    return view('pets.index', compact('status'));
})->name('pets.index');

Route::get('/pets/create', [PetController::class, 'create'])->name('pets.create');
