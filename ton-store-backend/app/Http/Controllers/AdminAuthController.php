<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminAuthController extends Controller
{
    /**
     * Login an admin
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Invalid credentials',
                'errors' => $validator->errors(),
            ], 401);
        }

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json([
                'error' => 'Invalid credentials',
            ], 401);
        }

        // Create a token for the admin
        $token = $admin->createToken('admin-login')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'admin' => [
                'id' => $admin->id,
                'email' => $admin->email,
                'name' => $admin->name,
            ],
        ]);
    }

    /**
     * Logout an admin
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out',
            'admin' => [
                'id' => $request->user()->id,
                'email' => $request->user()->email,
                'name' => $request->user()->name,
            ],
        ]);
    }

    /**
     * Get the authenticated admin's details
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request)
    {
        return response()->json([
            'message' => 'Admin user details',
            'admin' => [
                'id' => $request->user()->id,
                'email' => $request->user()->email,
                'name' => $request->user()->name,
            ],
        ]);
    }
}
