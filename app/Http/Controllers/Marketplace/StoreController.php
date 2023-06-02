<?php

namespace App\Http\Controllers\Marketplace;

use Illuminate\Http\Request;
use App\Models\Marketplace\Store;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class StoreController extends Controller
{
    /**
     * Display a listing of the stores.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stores = Store::all();

        return response()->json([
            'stores' => $stores
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created store in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole(['seller', 'admin'])) {
            return response()->json([
                'error' => 'Only users with seller or admin role can create a store.'
            ], Response::HTTP_FORBIDDEN);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'payment_address' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        // Check if the user already has a store
        $store = Store::where('user_id', $user->id)->first();

        if ($store) {
            return response()->json([
                'error' => 'User already has a store.'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Create a new store using firstOrCreate
        $store = Store::create(
            [
                'user_id' => $user->id,
                'name' => $request->name,
                'description' => $request->description,
                'payment_address' => $request->payment_address
            ]
        );

        if ($request->hasFile('image')) {
            $request->validate([
                'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $imageName = time() . '.' . $request->file('image')->extension();
            $request->file('image')->move(public_path('assets/marketplace/user-store'), $imageName);
            $imageUrl = asset('assets/marketplace/user-store/' . $imageName);
            $store->image = $imageUrl;
            $store->save();
        }

        return response()->json([
            'message' => 'Store created successfully',
            'store' => $store
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified store.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $store = Store::find($id);
        $user = Auth::user();

        if (!$store) {
            return response()->json([
                'error' => 'Store not found'
            ], Response::HTTP_NOT_FOUND);
        }

        if ($user->id != $store->user_id && !$user->hasRole(['seller', 'admin'])) {
            $store->setHidden(['payment_address', 'updated_at']);
        }

        return response()->json([
            'store' => $store
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified store in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateStore(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->hasAnyRole(['seller', 'admin'])) {
            return response()->json([
                'error' => 'Only users with seller or admin role can update a store.'
            ], Response::HTTP_FORBIDDEN);
        }

        $store = Store::find($id);

        if (!$store) {
            return response()->json([
                'error' => 'Store not found'
            ], Response::HTTP_NOT_FOUND);
        }

        if ($store->user_id != $user->id) {
            return response()->json([
                'error' => 'You are not authorized to update this store'
            ], Response::HTTP_FORBIDDEN);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'payment_address' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($request->hasFile('image')) {
            $request->validate([
                'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $imageName = time() . '.' . $request->file('image')->extension();
            $request->file('image')->move(public_path('assets/marketplace/user-store'), $imageName);
            $imageUrl = asset('assets/marketplace/user-store/' . $imageName);

            // Delete the old image file
            if ($store->image) {
                $image = substr(strrchr($store->image, "/"), 1);
                $this->deleteImageFile($image);
            }
            $store->image = $imageUrl;
        }

        $store->name = $request->name;
        $store->description = $request->description;
        $store->payment_address = $request->payment_address;
        $store->save();

        return response()->json([
            'message' => 'Store updated successfully',
            'store' => $store
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified store from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user->hasAnyRole(['seller', 'admin'])) {
            return response()->json([
                'error' => 'Only users with seller or admin role can delete a store.'
            ], Response::HTTP_FORBIDDEN);
        }

        $store = Store::find($id);

        if (!$store) {
            return response()->json([
                'error' => 'Store not found'
            ], Response::HTTP_NOT_FOUND);
        }

        if ($store->user_id != $user->id) {
            return response()->json([
                'error' => 'You are not authorized to delete this store'
            ], Response::HTTP_FORBIDDEN);
        }

        if ($store->image != '' || $store->image != null) {
            $image = substr(strrchr($store->image, "/"), 1);
            $this->deleteImageFile($image);
        }
        $store->delete();

        return response()->json([
            'message' => 'Store deleted successfully'
        ], Response::HTTP_OK);
    }

    /**
     * Delete the image file from the storage.
     *
     * @param  string  $imagePath
     * @return void
     */
    private function deleteImageFile($imagePath)
    {
        $filePath = public_path('assets/marketplace/user-store/' . $imagePath);
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}
