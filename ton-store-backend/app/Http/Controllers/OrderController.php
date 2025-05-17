<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\TonService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    protected $tonService;

    public function __construct(TonService $tonService)
    {
        $this->tonService = $tonService;
    }

    /**
     * Display a listing of the orders.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $orders = Order::with('product')->get();

        return response()->json([
            'message' => 'Orders fetched successfully.',
            'status' => 200,
            'orders' => $orders,
        ]);
    }

    /**
     * Store a newly created order in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'amount' => 'required|numeric|min:0',
            'transaction_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Verify the transaction on the TON blockchain
        $transactionVerification = $this->tonService->verifyTransaction($request->transaction_id);
        $transactionStatus = $transactionVerification['success'];

        // Create the order
        $order = Order::create([
            'id' => Str::uuid(),
            'user_id' => $request->user_id,
            'product_id' => $request->product_id,
            'amount' => $request->amount,
            'status' => $transactionStatus ? 1 : 0, // 1 = completed, 0 = pending
            'transaction_id' => $request->transaction_id,
        ]);

        return response()->json([
            'message' => 'Order processed successfully.',
            'status' => $transactionStatus ? 'Completed' : 'Pending',
            'order' => $order,
            'transaction_status' => $transactionStatus,
        ]);
    }

    /**
     * Display the specified order.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $order = Order::with('product')->find($id);

        if (!$order) {
            return response()->json([
                'error' => 'Order not found',
            ], 404);
        }

        return response()->json([
            'message' => 'Order fetched successfully.',
            'status' => 200,
            'order' => $order,
        ]);
    }

    /**
     * Update the specified order in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'error' => 'Order not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'amount' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $order->update([
            'amount' => $request->amount ?? $order->amount,
            'status' => $request->status ?? $order->status,
        ]);

        return response()->json([
            'message' => 'Order updated successfully.',
            'status' => 200,
            'order' => $order,
        ]);
    }

    /**
     * Remove the specified order from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'error' => 'Order not found',
            ], 404);
        }

        $order->delete();

        return response()->json([
            'message' => 'Order deleted.',
            'status' => 200,
        ]);
    }
}
