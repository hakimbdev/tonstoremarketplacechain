<?php

return [
    /*
    |--------------------------------------------------------------------------
    | TON API Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration settings for the TON API integration.
    | 
    */

    // The TON API endpoint URL
    'api_endpoint' => env('TON_API_ENDPOINT', 'https://toncenter.com/api/v2'),

    // Your TON API key
    'api_key' => env('TON_API_KEY', ''),

    // Network to use ('mainnet' or 'testnet')
    'network' => env('TON_NETWORK', 'testnet'),
]; 