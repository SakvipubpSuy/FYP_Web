<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Actions\Fortify\CreateNewUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $createNewUser = new CreateNewUser();
        $user = $createNewUser->create($request->all());
    
        return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            Log::info('User not found', ['email' => $request->email]);
            return response()->json([
                'message' => 'User does not exist.'
            ], 404);
        }

        if (!Hash::check($request->password, $user->password)) {
            Log::info('Password mismatch', ['email' => $request->email]);
            return response()->json([
                'message' => 'The provided credentials do not match our records in DB.'
            ], 422);
        }

        Log::info('Login successful', ['email' => $request->email]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token
        ]);
    }

    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Logged out successfully!!!'], 200);
        }
    }
}