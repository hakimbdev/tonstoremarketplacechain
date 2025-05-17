<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $products = Product::all();

        return response()->json([
            'message' => 'Products fetched successfully',
            'status' => 200,
            'product' => $products,
        ]);
    }

    /**
     * Store a newly created product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:stars,premium,username,number',
            'price' => 'required|numeric|min:0',
            'value' => 'nullable|numeric',
            'extra_data' => 'nullable|json',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $product = Product::create([
            'id' => Str::uuid(),
            'name' => $request->name,
            'type' => $request->type,
            'price' => $request->price,
            'value' => $request->value,
            'extra_data' => $request->extra_data ? json_decode($request->extra_data, true) : null,
        ]);

        return response()->json([
            'message' => 'Product created successfully',
            'status' => 201,
            'product' => $product,
        ], 201);
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
                'error' => 'Product not found',
            ], 404);
        }

        return response()->json([
            'message' => 'Product fetched successfully',
            'status' => 200,
            'product' => $product,
        ]);
    }

    /**
     * Update the specified product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'error' => 'Product not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'type' => 'sometimes|string|in:stars,premium,username,number',
            'price' => 'sometimes|numeric|min:0',
            'value' => 'nullable|numeric',
            'extra_data' => 'nullable|json',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $product->update([
            'name' => $request->name ?? $product->name,
            'type' => $request->type ?? $product->type,
            'price' => $request->price ?? $product->price,
            'value' => $request->value ?? $product->value,
            'extra_data' => $request->extra_data ? json_decode($request->extra_data, true) : $product->extra_data,
        ]);

        return response()->json([
            'message' => 'Product updated successfully',
            'status' => 200,
            'product' => $product,
        ]);
    }

    /**
     * Remove the specified product from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'error' => 'Product not found',
            ], 404);
        }

        $product->delete();

        return response()->json([
            'message' => 'Product deleted',
        ]);
    }
}
