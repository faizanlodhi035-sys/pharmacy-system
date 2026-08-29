<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MedicineSearchController;

Route::get('/medicines/search', [MedicineSearchController::class, 'search']);