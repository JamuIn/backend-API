<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Marketplace\StoreController;
use App\Http\Controllers\RekomendasiJamu\JamuController;
use App\Http\Controllers\RekomendasiJamu\IngredientController;
use App\Http\Controllers\RekomendasiJamu\JamuCategoryController;
use App\Http\Controllers\RekomendasiJamu\IngredientJamuController;

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

// AUTH CONTROLLER
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// ROUTES FOR REKOMEDASI JAMU
Route::apiResource('/jamu', JamuController::class);
Route::apiResource('/jamu-categories', JamuCategoryController::class);
Route::post('/ingredients/{id}', [IngredientController::class, 'updateIngredient']);
Route::apiResource('/ingredients', IngredientController::class);

// Many-to-many relationship for jamu and ingredients
Route::post('/jamu/{jamuId}/ingredients', [IngredientJamuController::class, 'attachIngredientToJamu'])
    ->name('jamu.ingredients.attach');
Route::delete('/jamu/{jamuId}/ingredients/{ingredientId}', [IngredientJamuController::class, 'detachIngredientFromJamu'])
    ->name('jamu.ingredients.detach');


// MARKETPLACE ROUTE
Route::apiResource('/stores', StoreController::class);
Route::post('/stores/{id}', [StoreController::class, 'updateStore']);
