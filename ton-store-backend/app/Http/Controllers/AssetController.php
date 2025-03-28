<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Services\TonService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AssetController extends Controller
{
    private TonService $tonService;

    public function __construct(TonService $tonService)
    {
        $this->tonService = $tonService;
    }

    public function index()
    {
        $assets = Asset::where('status', 'active')->get();
        return response()->json($assets);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'owner_address' => 'required|string',
            'image_url' => 'nullable|url',
            'metadata' => 'nullable|json',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Create asset on blockchain
            $blockchainData = $this->tonService->createAsset(
                $request->name,
                $request->description,
                $request->price,
                $request->owner_address
            );

            // Create asset in database
            $asset = Asset::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'owner_address' => $request->owner_address,
                'image_url' => $request->image_url,
                'metadata' => $request->metadata,
                'contract_address' => $blockchainData['contract_address'],
                'status' => 'active',
            ]);

            return response()->json($asset, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show(Asset $asset)
    {
        return response()->json($asset);
    }

    public function placeBid(Request $request, Asset $asset)
    {
        $validator = Validator::make($request->all(), [
            'bidder_address' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Place bid on blockchain
            $blockchainData = $this->tonService->placeBid(
                $asset,
                $request->bidder_address,
                $request->amount
            );

            // Create transaction record
            $transaction = $asset->transactions()->create([
                'transaction_hash' => $blockchainData['transaction_hash'],
                'from_address' => $request->bidder_address,
                'to_address' => $asset->owner_address,
                'amount' => $request->amount,
                'type' => 'bid',
                'status' => 'pending',
                'metadata' => $blockchainData,
            ]);

            return response()->json($transaction, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
} 