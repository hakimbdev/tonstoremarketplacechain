<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login or register a user using Telegram credentials
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function telegramLogin(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'telegram_id' => 'required|string',
                'username' => 'required|string',
                'first_name' => 'required|string',
                'last_name' => 'string|nullable',
                'auth_date' => 'required|string',
                'hash' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // In a production environment, you would verify the Telegram login hash here
            // For this implementation, we'll skip that step

            // Find or create the user
            $user = User::firstOrCreate(
                ['telegram_id' => $request->telegram_id],
                [
                    'username' => $request->username,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'status' => 1, // Active
                ]
            );

            // Update user information if it exists
            if ($user->wasRecentlyCreated === false) {
                $user->update([
                    'username' => $request->username,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                ]);
            }

            // Create a token for the user
            $token = $user->createToken('telegram-login')->plainTextToken;

            return response()->json([
                'message' => 'User logged in successfully',
                'status' => 200,
                'user' => $user,
                'token' => $token,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
