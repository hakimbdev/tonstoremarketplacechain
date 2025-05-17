<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TonService
{
    private string $apiEndpoint;
    private string $apiKey;

    public function __construct()
    {
        $this->apiEndpoint = config('ton.api_endpoint');
        $this->apiKey = config('ton.api_key');
    }

    public function createAsset(string $name, string $description, float $price, string $ownerAddress): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->apiEndpoint . '/assets', [
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'owner_address' => $ownerAddress,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Failed to create asset on TON blockchain', [
                'response' => $response->json(),
                'status' => $response->status(),
            ]);

            throw new \Exception('Failed to create asset on blockchain');
        } catch (\Exception $e) {
            Log::error('Error creating asset on TON blockchain', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function placeBid(Asset $asset, string $bidderAddress, float $amount): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->apiEndpoint . '/bids', [
                'asset_id' => $asset->id,
                'bidder_address' => $bidderAddress,
                'amount' => $amount,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Failed to place bid on TON blockchain', [
                'response' => $response->json(),
                'status' => $response->status(),
            ]);

            throw new \Exception('Failed to place bid on blockchain');
        } catch (\Exception $e) {
            Log::error('Error placing bid on TON blockchain', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Verify a transaction on the TON blockchain using the TON API
     *
     * @param string $transactionHash The transaction hash to verify
     * @return array Transaction data with success status
     */
    public function verifyTransaction(string $transactionHash): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->get('https://tonapi.io/v2/traces/' . $transactionHash);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => $data['transaction']['success'] ?? false,
                    'data' => $data,
                ];
            }

            Log::error('Failed to verify transaction on TON blockchain', [
                'response' => $response->json(),
                'status' => $response->status(),
            ]);

            return ['success' => false, 'data' => null];
        } catch (\Exception $e) {
            Log::error('Error verifying transaction on TON blockchain', [
                'error' => $e->getMessage(),
                'transaction_hash' => $transactionHash,
            ]);
            return ['success' => false, 'data' => null, 'error' => $e->getMessage()];
        }
    }

    public function getAssetBalance(string $address): float
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->get($this->apiEndpoint . '/balance/' . $address);

            if ($response->successful()) {
                return $response->json()['balance'];
            }

            throw new \Exception('Failed to get asset balance');
        } catch (\Exception $e) {
            Log::error('Error getting asset balance from TON blockchain', [
                'error' => $e->getMessage(),
                'address' => $address,
            ]);
            throw $e;
        }
    }
}