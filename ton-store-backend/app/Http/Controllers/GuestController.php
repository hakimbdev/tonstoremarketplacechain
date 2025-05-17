<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    /**
     * Display a paginated listing of star products.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function stars()
    {
        $stars = Product::ofType('stars')->paginate(10);

        return response()->json([
            'message' => 'Products fetched successfully',
            'status' => 200,
            'stars' => $stars,
        ]);
    }

    /**
     * Display a paginated listing of number products.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function numbers()
    {
        $numbers = Product::ofType('number')->paginate(10);

        return response()->json([
            'message' => 'Products fetched successfully',
            'status' => 200,
            'numbers' => $numbers,
        ]);
    }

    /**
     * Display a paginated listing of username products.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function usernames()
    {
        $usernames = Product::ofType('username')->paginate(10);

        return response()->json([
            'message' => 'Products fetched successfully',
            'status' => 200,
            'usernames' => $usernames,
        ]);
    }

    /**
     * Display a paginated listing of premium products.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function premiums()
    {
        $premiums = Product::ofType('premium')->paginate(10);

        return response()->json([
            'message' => 'Products fetched successfully',
            'status' => 200,
            'premium' => $premiums,
        ]);
    }

    /**
     * Display the specified product.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'error' => 'No product found with ID.',
            ], 404);
        }

        return response()->json([
            'message' => 'Products fetched successfully',
            'status' => 200,
            'product' => $product,
        ]);
    }
}
