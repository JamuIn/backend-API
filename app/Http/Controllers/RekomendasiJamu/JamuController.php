<?php

namespace App\Http\Controllers\RekomendasiJamu;

use App\Http\Controllers\Controller;
use App\Models\RekomendasiJamu\Jamu;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JamuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        $jamu = Jamu::all();
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
            'name' => 'required|string',
            'description' => 'required|string',
            'ingredients' => 'required|string',
            'steps' => 'required|string',
            'source' => 'nullable|string',
        ]);

        $jamu = Jamu::create($request->all());
        return response()->json(['data' => $jamu], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show($id)
    {
        $jamu = Jamu::findOrFail($id);
        return response()->json(['data' => $jamu], Response::HTTP_OK);
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
            'category_id' => 'required|integer',
            'name' => 'required|string',
            'description' => 'required|string',
            'ingredients' => 'required|string',
            'steps' => 'required|string',
            'source' => 'nullable|string',
        ]);

        $jamu = Jamu::findOrFail($id);
        $jamu->update($request->all());

        return response()->json(['data' => $jamu], Response::HTTP_OK);
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
        $jamu->delete();

        return response()->json(['message' => 'Jamu deleted successfully'], Response::HTTP_OK);
    }
}
