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

        return response()->json(['data' => $ingredients], Response::HTTP_OK);
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
        $imagePath = asset('assets/rekomendasi-jamu/ingredients/' . $imageName);

        $ingredient = Ingredient::create([
            'name' => $request->name,
            'image' => $imagePath,
        ]);

        return response()->json([
            'data' => $ingredient
        ], Response::HTTP_CREATED);
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

        return response()->json(['data' => $ingredient], Response::HTTP_OK);
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
            $imageUrl = asset('assets/rekomendasi-jamu/ingredients/' . $imageName);

            // Delete the old image file
            if ($ingredient->image) {
                $this->deleteImageFile($ingredient->image);
            }

            $ingredient->image = $imageUrl;
        }

        $ingredient->save();

        return response()->json([
            'data' => $ingredient
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
