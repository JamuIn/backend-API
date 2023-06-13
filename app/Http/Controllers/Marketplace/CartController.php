<?php

namespace App\Http\Controllers\Marketplace;

use Illuminate\Http\Request;
use App\Models\Marketplace\Cart;
use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\Marketplace\Product;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'role:customer'])->except(['index', 'show']);
        $this->middleware(['auth:sanctum', 'role:admin'])->only(['index']);
    }
    /**
     * Display a listing of the carts.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        $carts = CartResource::collection(Cart::all());

        return response()->json([
            'carts' => $carts
        ], Response::HTTP_OK);
    }

    /**
     * Retrieve a specific user's cart.
     *
     * @param  int  $userId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getUserCart()
    {
        $user = Auth::user();
        $carts = CartResource::collection(Cart::where('user_id', $user->id)->get());

        return response()->json(['carts' => $carts], Response::HTTP_OK);
    }

    /**
     * Store a newly created cart in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer',
        ]);

        $product = Product::find($request->product_id);
        // check product stock
        if ($product->stock < $request->quantity) {
            return response()->json([
                'message' => 'Error. Product of ' . $product->name . ' not found in stock'
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        $cart = Cart::create([
            'user_id' => Auth::user()->id,
            'product_id' => $request->input('product_id'),
            'quantity' => $request->input('quantity'),
        ]);

        return response()->json([
            'cart' => new CartResource($cart)
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified cart.
     *
     * @param  \App\Models\Cart  $cart
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(Cart $cart)
    {
        return response()->json([
            'cart' => new CartResource($cart)
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified cart in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cart  $cart
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function update(Request $request, Cart $cart)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer',
        ]);

        $cart->product_id = $request->input('product_id');
        $cart->quantity = $request->input('quantity');
        $cart->save();

        return response()->json(['cart' => $cart], Response::HTTP_OK);
    }

    /**
     * Remove the specified cart from storage.
     *
     * @param  \App\Models\Cart  $cart
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function destroy(Cart $cart)
    {
        $cart->delete();

        return response()->json(['message' => 'Cart of id ' . $cart->id . ' was successfully deleted'], Response::HTTP_OK);
    }
}
