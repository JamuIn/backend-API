<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Marketplace\CartController;
use App\Http\Controllers\Marketplace\OrderController;
use App\Http\Controllers\Marketplace\StoreController;
use App\Http\Controllers\RekomendasiJamu\JamuController;
use App\Http\Controllers\Marketplace\CartProductsController;
use App\Http\Controllers\Marketplace\StoreProductController;
use App\Http\Controllers\RekomendasiJamu\JamuUserController;
use App\Http\Controllers\Marketplace\ProductReviewController;
use App\Http\Controllers\RekomendasiJamu\IngredientController;
use App\Http\Controllers\RekomendasiJamu\JamuCategoryController;
use App\Http\Controllers\Marketplace\IngredientProductController;
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
Route::post('/user/{id}', [UserController::class, 'updateUser']);
Route::apiResource('/users', UserController::class)->except('update');

// ROUTES FOR REKOMEDASI JAMU
Route::post('/jamu/{id}', [JamuController::class, 'updateJamu'])->name('jamu.update');
Route::post('/ingredients/{id}', [IngredientController::class, 'updateIngredient']);
Route::apiResource('/jamu', JamuController::class);
Route::apiResource('/jamu-categories', JamuCategoryController::class);
Route::apiResource('/ingredients', IngredientController::class);
// Many-to-many relationship for jamu and ingredients
Route::post('/jamu/{jamuId}/ingredients', [IngredientJamuController::class, 'attachIngredientToJamu'])
    ->name('jamu.ingredients.attach');
Route::delete('/jamu/{jamuId}/ingredients/{ingredientId}', [IngredientJamuController::class, 'detachIngredientFromJamu'])
    ->name('jamu.ingredients.detach');
// Many to many relationship for Jamu and User (Add jamu ass favorite)
Route::get('/user/favorites', [JamuUserController::class, 'showFavorites']);
Route::post('/jamu/{jamuId}/favorite', [JamuUserController::class, 'addJamuToUserFavorite'])
    ->name('jamu.favorite.attach');
Route::delete('/jamu/{jamuId}/favorite', [JamuUserController::class, 'detachJamuFromUserFavorite'])
    ->name('jamu.favorite.detach');

// MARKETPLACE ROUTE -PRODUCTS
Route::get('/products', [StoreProductController::class, 'indexAll'])->name('products.indexAll');
Route::get('/products/{id}', [StoreProductController::class, 'showProduct'])->name('products.showProduct');
Route::post('/stores/{store}/products/{product}', [StoreProductController::class, 'updateProduct'])->name('products.update');
Route::resource('/stores.products', StoreProductController::class)->except(['create', 'edit']);
// Many-to-many relationship for product and ingredients
Route::post('/product/{productId}/ingredients', [IngredientProductController::class, 'attachIngredientToProduct'])
    ->name('product.ingredients.attach');
Route::delete('/product/{productId}/ingredients/{ingredientId}', [IngredientProductController::class, 'detachIngredientFromProduct'])
    ->name('product.ingredients.detach');

// MARKETPLACE ROUTE -STORE
Route::post('/stores/{id}', [StoreController::class, 'updateStore']);
Route::apiResource('/stores', StoreController::class);

// MARKETPLACE ROUTES --CART
Route::get('/user/{userId}/carts', [CartController::class, 'getUserCart']);
Route::apiResource('/carts', CartController::class);

// MARKETPLACE --ORDERS
Route::get('/user/{userId}/orders', [OrderController::class, 'getUserOrder']);
Route::post('/orders/{order}', [OrderController::class, 'updateOrder']);
Route::apiResource('/orders', OrderController::class);

// MARKETPLACE --CHECKOUT
Route::get('/confirm-checkout', [CartProductsController::class, 'confirmCheckout']);
Route::post('/checkout', [CartProductsController::class, 'checkout']);

// MARKETPLACE -- PRODUCT REVIEWS
Route::get('/reviews', [ProductReviewController::class, 'indexAll'])->name('reviews.indexAll');
Route::resource('/products.reviews', ProductReviewController::class)->except(['create', 'edit']);
