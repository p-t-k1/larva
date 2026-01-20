<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pets.index');
})->name('pets.index');
