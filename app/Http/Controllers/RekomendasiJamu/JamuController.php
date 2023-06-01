<?php

namespace App\Http\Controllers\RekomendasiJamu;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\JamuResource;
use App\Models\RekomendasiJamu\Jamu;
use App\Models\RekomendasiJamu\Ingredient;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Permission\Middleware\RoleMiddleware;

class JamuController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'role:admin'])->except(['index', 'show']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        // we use JamuResource to show attached main ingredient
        $jamu = JamuResource::collection(Jamu::all());
        return response()->json(['data' => $jamu], Response::HTTP_OK);
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
            'category_id' => 'required|integer',
            'main_ingredient_id' => 'required|integer',
            'name' => 'required|string',
            'description' => 'required|string',
            'ingredients' => 'required|string',
            'steps' => 'required|string',
            'source' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imageName = time() . '.' . $request->file('image')->extension();
        $request->image->move(public_path('assets/rekomendasi-jamu/jamu'), $imageName);
        $imagePath = asset('assets/rekomendasi-jamu/jamu/' . $imageName);

        $jamu = Jamu::create([
            'category_id' => (int)$request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'ingredients' => $request->ingredients,
            'steps' => $request->steps,
            'source' => $request->source,
            'image' => $imagePath
        ]);
        // attach main ingredient to Jamu
        $ingredient = Ingredient::query()->findOrFail($request->input('main_ingredient_id'));
        $jamu->ingredients()->attach($ingredient);

        return response()->json(
            [
                'message' => 'Jamu has succesfully added',
                'data' => new JamuResource($jamu)
            ],
            Response::HTTP_CREATED
        );
        return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(Jamu $jamu)
    {
        return response()->json([
            'data' => new JamuResource($jamu)
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateJamu(Request $request, $id)
    {
        $jamu = Jamu::findOrFail($id);

        $request->validate([
            'category_id' => 'required|integer',
            'name' => 'required|string',
            'description' => 'required|string',
            'ingredients' => 'required|string',
            'steps' => 'required|string',
            'source' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // delete old image
            if ($jamu->image != '') {
                $old_image = substr(strrchr($jamu->image, "/"), 1);
                $this->deleteImageFile($old_image);
            }

            $imageName = time() . '.' . $request->file('image')->extension();
            $destinationPath = public_path('assets/rekomendasi-jamu/jamu');
            $request->file('image')->move($destinationPath, $imageName);

            $image_url = asset('assets/rekomendasi-jamu/jamu/' . $imageName);
            $jamu->image = $image_url;

            $jamu->save();
        }

        $jamu->update([
            'category_id' => (int)$request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'ingredients' => $request->ingredients,
            'steps' => $request->steps,
            'source' => $request->source
        ]);

        return response()->json([
            'data' => new JamuResource($jamu)
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
        $jamu = Jamu::findOrFail($id);
        $image = substr(strrchr($jamu->image, "/"), 1);
        $this->deleteImageFile($image);
        $ingredient = Ingredient::query()->findOrFail($jamu->ingredients()->first()->id);
        $jamu->ingredients()->detach($ingredient);

        $jamu->delete();

        return response()->json(['message' => 'Jamu deleted successfully'], Response::HTTP_OK);
    }

    protected function deleteImageFile($fileName)
    {
        $filePath = public_path('assets/rekomendasi-jamu/jamu/' . $fileName);
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}
