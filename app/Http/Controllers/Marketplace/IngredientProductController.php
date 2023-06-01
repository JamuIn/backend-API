<?php

namespace App\Http\Controllers\Marketplace;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\Marketplace\Product;
use App\Models\RekomendasiJamu\Ingredient;

class IngredientProductController extends Controller
{
    public function attachIngredientToProduct(Request $request, $productId)
    {
        $request->validate([
            'ingredient_id' => 'required|exists:ingredients,id',
        ]);

        $ingredient = Ingredient::query()->findOrFail($request->input('ingredient_id'));
        $product = Product::query()->findOrFail($productId);

        $product->ingredients()->attach($ingredient);

        return response()->json(['message' => 'Ingredient attached to Product successfully.'], Response::HTTP_CREATED);
    }

    public function detachIngredientFromProduct($productId, $ingredientId)
    {
        $product = Product::query()->findOrFail($productId);

        $product->ingredients()->detach($ingredientId);

        return response()->json(['message' => 'Ingredient detached from Product successfully.'], Response::HTTP_OK);
    }
}
