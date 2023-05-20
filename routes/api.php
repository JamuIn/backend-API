<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RekomendasiJamu\JamuController;
use App\Http\Controllers\RekomendasiJamu\IngredientController;
use App\Http\Controllers\RekomendasiJamu\JamuCategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('/jamu', JamuController::class);
Route::apiResource('/jamu-categories', JamuCategoryController::class);
Route::post('/ingredients/{id}', [IngredientController::class, 'updateIngredient']);
Route::apiResource('/ingredients', IngredientController::class);
