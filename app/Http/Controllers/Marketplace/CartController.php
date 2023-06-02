<?php

namespace App\Http\Controllers\Marketplace;

use Illuminate\Http\Request;
use App\Models\Marketplace\Cart;
use App\Http\Controllers\Controller;
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
        $carts = Cart::all();

        return response()->json(['carts' => $carts], Response::HTTP_OK);
    }

    /**
     * Retrieve a specific user's cart.
     *
     * @param  int  $userId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getUserCart($userId)
    {
        $carts = Cart::where('user_id', $userId)->get();

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

        $cart = Cart::create([
            'user_id' => Auth::user()->id,
            'product_id' => $request->input('product_id'),
            'quantity' => $request->input('quantity'),
        ]);

        return response()->json(['cart' => $cart], Response::HTTP_CREATED);
    }

    /**
     * Display the specified cart.
     *
     * @param  \App\Models\Cart  $cart
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(Cart $cart)
    {
        return response()->json(['cart' => $cart], Response::HTTP_OK);
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

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
