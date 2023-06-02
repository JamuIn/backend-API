<?php

namespace App\Http\Controllers\Marketplace;

use Illuminate\Http\Request;
use App\Models\Marketplace\Order;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'role:customer'])->except(['index']);
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

    public function getUserOrder($userId)
    {
        $last_order = Order::where('user_id', $userId)->first();
        if ($last_order->user_id != $userId) {
            return response()->json([
                'error' => 'You are not authorized to see this order'
            ], Response::HTTP_FORBIDDEN);
        };

        $orders = Order::where('user_id', $userId)->get();


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
        ]);

        $order = new Order;
        $order->user_id = auth()->user()->id;
        $order->total_price = $request->input('total_price');
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
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'total_price' => 'required|integer',
        ]);

        $order->total_price = $request->input('total_price');
        $order->save();

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
