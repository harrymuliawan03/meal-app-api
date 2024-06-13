<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\MealController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    // categories
    Route::get('/categories', [CategoryController::class, 'getAll']);
    Route::post('/categories', [CategoryController::class, 'create']);
    Route::put('/categories/{id}', [CategoryController::class, 'edit']);
    Route::delete('/categories/{id}', [CategoryController::class, 'delete']);

    // meal
    Route::get('/meals', [MealController::class, 'index']);
    Route::get('/meals/{id}', [MealController::class, 'show']);
    Route::post('/meals', [MealController::class, 'store']);
    Route::put('/meals/{id}', [MealController::class, 'update']);
    Route::delete('/meals/{id}', [MealController::class, 'destroy']);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
