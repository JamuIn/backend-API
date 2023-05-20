<?php

namespace App\Http\Controllers\RekomendasiJamu;

use App\Http\Controllers\Controller;
use App\Models\RekomendasiJamu\Ingredient;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IngredientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        $ingredients = Ingredient::all();

        $ingredientsData = $ingredients->map(function ($ingredient) {
            $imagePath = $ingredient->image ? asset('assets/rekomendasi-jamu/ingredients/' . $ingredient->image) : null;
            return [
                'id' => $ingredient->id,
                'name' => $ingredient->name,
                'image' => $imagePath,
            ];
        });

        return response()->json(['data' => $ingredientsData], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imageName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('assets/rekomendasi-jamu/ingredients'), $imageName);

        $ingredient = Ingredient::create([
            'name' => $request->name,
            'image' => $imageName,
        ]);

        $imagePath = asset('assets/rekomendasi-jamu/ingredients/' . $imageName);

        return response()->json(['data' => $ingredient, 'image_path' => $imagePath], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show($id)
    {
        $ingredient = Ingredient::findOrFail($id);

        $imagePath = $ingredient->image ? asset('assets/rekomendasi-jamu/ingredients/' . $ingredient->image) : null;

        $ingredientData = [
            'id' => $ingredient->id,
            'name' => $ingredient->name,
            'image' => $imagePath,
        ];

        return response()->json(['data' => $ingredientData], Response::HTTP_OK);
    }


    // DUMMY UPDATE METHOD
    public function update(Request $request, $id)
    {
        if ($request->method() === 'PUT') {
            return response()->json(
                [
                    'message' => 'The PUT method is not supported for updating Ingredients.  
                    Please use the POST method instead.'
                ],
                Response::HTTP_METHOD_NOT_ALLOWED
            );
        };
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Symfony\Component\HttpFoundation\Response
     * THIS Function USE POST METHOD BECAUSE PUT METHOD NOT SUPPORTED IN LARAVEL FOR UPLOADING FILE
     */
    public function updateIngredient(Request $request, $id)
    {
        $ingredient = Ingredient::findOrFail($id);

        if ($request->filled('name')) {
            $ingredient->name = $request->input('name');
        }

        if ($request->hasFile('image')) {
            $request->validate([
                'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $imageName = time() . '.' . $request->file('image')->extension();
            $request->file('image')->move(public_path('assets/rekomendasi-jamu/ingredients'), $imageName);

            // Delete the old image file
            if ($ingredient->image) {
                $this->deleteImageFile($ingredient->image);
            }

            $ingredient->image = $imageName;
        }

        $ingredient->save();

        $imageUrl = $ingredient->image ? asset('assets/rekomendasi-jamu/ingredients/' . $ingredient->image) : null;

        return response()->json([
            'data' => $ingredient,
            'image_url' => $imageUrl,
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function destroy($id)
    {
        $ingredient = Ingredient::findOrFail($id);
        $imagePath = $ingredient->image;

        $ingredient->delete();

        // Delete the image file
        if ($imagePath) {
            $this->deleteImageFile($imagePath);
        }

        return response()->json(['message' => 'Ingredient deleted successfully'], Response::HTTP_OK);
    }

    /**
     * Delete the image file from the storage.
     *
     * @param  string  $imagePath
     * @return void
     */
    private function deleteImageFile($imagePath)
    {
        $filePath = public_path('assets/rekomendasi-jamu/ingredients/' . $imagePath);
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}
