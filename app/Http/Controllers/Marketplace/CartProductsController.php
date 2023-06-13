<?php

namespace App\Http\Controllers\Marketplace;

use Illuminate\Http\Request;
use App\Models\Marketplace\Cart;
use App\Models\Marketplace\Order;
use Illuminate\Support\Facades\DB;
use App\Models\Marketplace\Product;
use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use Symfony\Component\HttpFoundation\Response;

class CartProductsController extends Controller
{

    public function confirmCheckout(Request $request)
    {
        $request->validate([
            'payment_address' => 'required|string'
        ]);

        $user = auth()->user();
        $carts = CartResource::collection(Cart::where('user_id', $user->id)->get());
        $address = $user->address;
        $payment_address = $request->payment_address;

        // get product detail
        $product_detail = [];
        $total_price = 0;
        foreach ($carts as $cart) {
            $price = $cart->product->price * $cart->quantity;
            $product_detail[] = $cart->product->name . " x" . $cart->quantity . " = " . $price;
            $total_price += $price;
        }

        return response()->json([
            'message' => 'Confirm Checkout?',
            'cart' => $carts,
            'user_address' => $address,
            'payment_address' => $payment_address,
            'product_detail' => $product_detail,
            'shipping cost' => 10000,
            'total_price' => $total_price + 10000

        ], Response::HTTP_OK);
    }

    public function checkout()
    {
        $cart = Cart::with('product')->where('user_id', auth()->user()->id)->get();
        $products = Product::select('id', 'stock')
            ->whereIn('id', $cart->pluck('product_id'))
            ->pluck('stock', 'id');
        // check product quantity
        foreach ($cart as $cartProduct) {
            if (
                !isset($products[$cartProduct->product_id])
                || $products[$cartProduct->product_id] < $cartProduct->quantity
            ) {
                return response()->json([
                    'message' => 'Error. Product ' . $cartProduct->product->name . ' not found in stock'
                ], Response::HTTP_NOT_ACCEPTABLE);
            }
        }
        try {
            DB::transaction(function () use ($cart) {
                // create the order
                $order = Order::create([
                    'user_id' => auth()->user()->id,
                    'total_price' => 0,
                    'status' => 'Unpaid'
                ]);

                $products_ordered = [];
                $total_price = 0;
                $payment_address = "Gopay 0881 2345 6789 - A.n Hakam Royhan A";

                // attach each product to order in OrderProduct
                foreach ($cart as $cartProduct) {
                    $order->products()->attach($cartProduct->product_id, [
                        'quantity' => $cartProduct->quantity,
                        'price' => $cartProduct->product->price
                    ]);
                    // count total price
                    $price = $cartProduct->quantity * $cartProduct->product->price;
                    $order->increment('total_price', $price);
                    $total_price += $price;
                    // decrease product stock
                    Product::find($cartProduct->product_id)->decrement('stock', $cartProduct->quantity);
                    // add product to response
                    $products_ordered[] = $cartProduct->product->name . ', quantity: ' . $cartProduct->quantity;
                }
                // add shipping fee
                $order->increment('total_price', 10000);
                // delete cart after checkout
                Cart::where('user_id', auth()->user()->id)->delete();

                return response()->json([
                    'message' => 'Checkout success',
                    'order' => $order,
                    'products' => $products_ordered,
                    'total_price' => $total_price + 10000,
                    'payment_address' => $payment_address
                ], Response::HTTP_CREATED);
            });
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error Happened ' . $exception->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
