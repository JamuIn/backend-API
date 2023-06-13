<?php

namespace App\Http\Controllers\Marketplace;

use Illuminate\Http\Request;
use App\Models\Marketplace\Order;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'role:customer'])->except(['updateOrder']);
        $this->middleware(['auth:sanctum', 'role:admin'])->only(['index']);
    }

    /**
     * Display a listing of the orders.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        $orders = Order::all();

        return response()->json(['orders' => $orders], Response::HTTP_OK);
    }

    public function getUserOrder()
    {
        $user = Auth::user();
        $last_order = Order::where('user_id', $user->id)->first();
        if ($last_order->user_id != $user->id) {
            return response()->json([
                'error' => 'You are not authorized to see this order'
            ], Response::HTTP_FORBIDDEN);
        };

        $orders = Order::where('user_id', $user->id)->get();


        return response()->json(['orders' => $orders], Response::HTTP_OK);
    }

    /**
     * Store a newly created order in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'total_price' => 'required|integer',
            'status' => 'required|string'
        ]);

        $order = new Order;
        $order->user_id = auth()->user()->id;
        $order->total_price = $request->input('total_price');
        $order->status = $request->input('status');
        $order->save();

        return response()->json(['order' => $order], Response::HTTP_CREATED);
    }

    /**
     * Display the specified order.
     *
     * @param  \App\Models\Order  $order
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(Order $order)
    {
        return response()->json(['order' => $order], Response::HTTP_OK);
    }

    /**
     * Update the specified order in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateOrder(Request $request, Order $order)
    {
        $user = auth()->user();
        $role = User::find($user->id)->getRoleNames();

        if ($role[0] == 'seller') {
            $request->validate([
                'status' => 'required|string'
            ]);

            $order->status = $request->input('status');
            $order->save();
        }

        if ($request->hasFile('payment_proof')) {
            $request->validate([
                'payment_proof' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $imageName = time() . '.' . $request->file('payment_proof')->extension();
            $request->file('payment_proof')->move(public_path('assets/marketplace/payment_proofs'), $imageName);
            $imageUrl = asset('assets/marketplace/payment_proofs/' . $imageName);

            // Delete the old image file
            if ($order->payment_proof) {
                $image = substr(strrchr($order->payment_proof, "/"), 1);
                $this->deleteImageFile($image);
            }
            $order->payment_proof = $imageUrl;
        }

        return response()->json(['order' => $order], Response::HTTP_OK);
    }

    /**
     * Remove the specified order from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function destroy(Order $order)
    {
        $order->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
