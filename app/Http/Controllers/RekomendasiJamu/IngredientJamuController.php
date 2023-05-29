<?php

namespace App\Http\Controllers\RekomendasiJamu;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\RekomendasiJamu\Jamu;
use App\Models\RekomendasiJamu\Ingredient;

class IngredientJamuController extends Controller
{
    public function attachIngredientToJamu(Request $request, $jamuId)
    {
        $request->validate([
            'ingredient_id' => 'required|exists:ingredients,id',
        ]);

        $ingredient = Ingredient::query()->findOrFail($request->input('ingredient_id'));
        $jamu = Jamu::query()->findOrFail($jamuId);

        var_dump($ingredient);
        // $jamu->ingredients()->attach($ingredient);

        // return response()->json(['message' => 'Ingredient attached to Jamu successfully.'], Response::HTTP_CREATED);
    }

    public function detachIngredientFromJamu($jamuId, $ingredientId)
    {
        $jamu = Jamu::query()->findOrFail($jamuId);

        $jamu->ingredients()->detach($ingredientId);

        return response()->json(['message' => 'Ingredient detached from Jamu successfully.'], Response::HTTP_OK);
    }
}
