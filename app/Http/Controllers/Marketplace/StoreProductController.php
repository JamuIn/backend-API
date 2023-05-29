<?php

namespace App\Http\Controllers\Marketplace;

use Illuminate\Http\Request;
use App\Models\Marketplace\Store;
use App\Models\Marketplace\Product;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StoreProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'role:seller'])->except(['indexAll', 'index', 'show']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Store $store)
    {
        $products = Product::where('store_id', $store->id)->get()->all();

        return response()->json([
            'products' => $products,
        ], Response::HTTP_OK);
    }

    // SHOW ALL PRODUCT 
    public function indexAll()
    {
        $products = Product::all();

        return response()->json([
            'products' => $products,
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Store $store)
    {
        if ($store->user_id != Auth::user()->id) {
            return response()->json([
                'error' => 'You are not authorized to do operations this store'
            ], Response::HTTP_FORBIDDEN);
        }

        $request->validate([
            'jamu_category_id' => 'required|integer',
            'name' => 'required|string',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price' => 'required|integer',
            'stock' => 'required|integer'
        ]);

        $imagePath = $this->handleFileUpload($request);

        $product = Product::create([
            'store_id' => $store->id,
            'jamu_category_id' => (int)$request->input('jamu_category_id'),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'image' => $imagePath,
            'price' => $request->input('price'),
            'stock' => $request->input('stock'),
        ]);

        return response()->json([
            'message' => 'New Product ' . $product->name . ' was successfully created',
            'product' => $product,
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Store $store, Product $product)
    {
        $product_in_store = Product::where('id', $product->id)->where('store_id', $store->id)->first();

        return response()->json([
            'product' => $product_in_store,
        ], Response::HTTP_OK);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        if ($request->method() === 'PUT') {
            return response()->json(
                [
                    'message' => 'The PUT method is not supported for updating Store.  Please use the POST method instead.'
                ],
                Response::HTTP_METHOD_NOT_ALLOWED
            );
        };
    }

    public function updateProduct(Request $request, $store, $product)
    {
        $store = Store::where('id', $store)->first();
        if ($store->user_id != Auth::user()->id) {
            return response()->json([
                'error' => 'You are not authorized to do operations this store'
            ], Response::HTTP_FORBIDDEN);
        }

        $request->validate([
            'jamu_category_id' => 'required|integer',
            'name' => 'required|string',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price' => 'required|integer',
            'stock' => 'required|integer'
        ]);

        $target_product = Product::findOrFail($product);

        // Handle file upload
        if ($request->hasFile('image')) {
            if ($target_product->image != '') {
                $old_image = substr(strrchr($target_product->image, "/"), 1);
                $this->deleteFileIfExists($old_image);
            }
            // update image
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('assets/marketplace/user-store/products'), $imageName);
            $image_url = asset('assets/marketplace/user-store/products/' . $imageName);
            $target_product->image = $image_url;
        }
        $target_product->save();

        $target_product->update([
            'store_id' => $request->input('store_id', $target_product->store_id),
            'jamu_category_id' => $request->input('jamu_category_id', $target_product->jamu_category_id),
            'name' => $request->input('name', $target_product->name),
            'description' => $request->input('description', $target_product->description),
            'price' => $request->input('price', $target_product->price),
            'stock' => $request->input('stock', $target_product->stock),
        ]);

        return response()->json([
            'updated product' => $target_product
        ], Response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Store $store, Product $product)
    {
        if ($store->user_id != Auth::user()->id) {
            return response()->json([
                'error' => 'You are not authorized to do operations this store'
            ], Response::HTTP_FORBIDDEN);
        }
        $product = Product::findOrFail($product->id);

        $image = substr(strrchr($product->image, "/"), 1);
        $this->deleteFileIfExists($image);

        $product->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }


    protected function deleteFileIfExists($fileName)
    {
        $filePath = 'assets/marketplace/user-store/products/';
        if (file_exists(public_path($filePath . $fileName))) {
            unlink(public_path($filePath . $fileName));
        }
    }

    protected function handleFileUpload(Request $request)
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('assets/marketplace/user-store/products'), $imageName);
            $image_url = asset('assets/marketplace/user-store/products/' . $imageName);
            return $image_url;
        }

        return null;
    }
}
