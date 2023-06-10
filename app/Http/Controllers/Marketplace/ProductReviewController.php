<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\Marketplace\Product;
use App\Models\Marketplace\Review;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'role:customer'])->except('indexAll', 'index', 'show');
        $this->middleware(['auth:sanctum', 'role:admin'])->only('indexAll');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Product $product)
    {
        $reviews = Review::where('product_id', $product->id)->get()->all();
        return response()->json(['reviews' => $reviews], Response::HTTP_OK);
    }

    // SHOW ALL Reviews
    public function indexAll()
    {
        $reviews = Review::all();

        return response()->json([
            'reviews' => $reviews
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'review' => 'required',
            'rating' => 'required',
        ]);

        $review = new Review();
        $review->user_id = auth()->user()->id;
        $review->product_id = $product->id;
        $review->review = $request->review;
        $review->rating = $request->rating;
        $review->save();

        return response()->json(['message' => 'Review created successfully', 'review' => $review], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product, Review $review)
    {
        $review = Review::findOrFail($review->id);
        return response()->json(['review' => $review], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product, Review $review)
    {
        $request->validate([
            'review' => 'required',
            'rating' => 'required',
        ]);

        $review = Review::findOrFail($review->id);

        if (auth()->user()->id != $review->user_id) {
            return response()->json(['message' => 'You are not allowed to edit this review'], 401);
        }

        $review->user_id = auth()->user()->id ?? $review->user_id;
        $review->product_id = $product->id ?? $review->product_id;
        $review->review = $request->review;
        $review->rating = $request->rating;
        $review->save();

        return response()->json(['message' => 'Review updated successfully', 'review' => $review], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product, Review $review)
    {
        $review = Review::findOrFail($review->id);
        if (auth()->user()->id != $review->user_id) {
            return response()->json(['message' => 'You are not allowed to delete this review'], 401);
        }

        $review->delete();

        return response()->json(['message' => 'Review deleted successfully'], Response::HTTP_OK);
    }
}
