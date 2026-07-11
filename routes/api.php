<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::get('/health',      [ApiController::class, 'health']);
Route::get('/attractions', [ApiController::class, 'attractions']);
Route::get('/guides',      [ApiController::class, 'guides']);
Route::get('/hotels',      [ApiController::class, 'hotels']);
Route::get('/events',      [ApiController::class, 'events']);
Route::get('/search',      [ApiController::class, 'search']);
Route::get('/stats',       [ApiController::class, 'stats']);
