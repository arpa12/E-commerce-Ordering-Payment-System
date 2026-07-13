<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        Log::info('User registration attempt', ['email' => $request->input('email')]);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            Log::warning('User registration failed validation', [
                'email' => $request->input('email'),
                'errors' => $validator->errors()->toArray()
            ]);
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            Log::info('User registered successfully', ['user_id' => $user->id, 'email' => $user->email]);

            return response()->json([
                'message' => 'User registered successfully',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user
            ], 201);
        } catch (\Exception $e) {
            Log::error('User registration error', [
                'email' => $request->input('email'),
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'An error occurred during registration. Please try again later.'
            ], 500);
        }
    }

    /**
     * Log in a user.
     */
    public function login(Request $request)
    {
        Log::info('User login attempt', ['email' => $request->input('email')]);

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->input('email'))->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            Log::warning('User login failed (invalid credentials)', ['email' => $request->input('email')]);
            return response()->json([
                'message' => 'Invalid email or password'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        Log::info('User logged in successfully', ['user_id' => $user->id, 'email' => $user->email]);

        return response()->json([
            'message' => 'Logged in successfully',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 200);
    }
}

