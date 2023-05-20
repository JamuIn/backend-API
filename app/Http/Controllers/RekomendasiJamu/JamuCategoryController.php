<?php

namespace App\Http\Controllers\RekomendasiJamu;

use App\Http\Controllers\Controller;
use App\Models\RekomendasiJamu\JamuCategory;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JamuCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        $categories = JamuCategory::all();
        return response()->json(['data' => $categories], Response::HTTP_OK);
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
        ]);

        $category = JamuCategory::create($request->all());
        return response()->json(['data' => $category], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show($id)
    {
        $category = JamuCategory::findOrFail($id);
        return response()->json(['data' => $category], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $category = JamuCategory::findOrFail($id);
        $category->update($request->all());

        return response()->json(['data' => $category], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function destroy($id)
    {
        $category = JamuCategory::findOrFail($id);
        $category->delete();

        return response()->json(['message' => 'Jamu category deleted successfully'], Response::HTTP_OK);
    }
}
