<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Actions\Fortify\CreateNewUser;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\PasswordReset;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Proceed with user creation
        $createNewUser = new CreateNewUser();
        $user = $createNewUser->create($request->all());

        // Generate a token for the newly registered user
        $token = $user->createToken('auth_token')->plainTextToken;

        // Return user data and token
        return response()->json([
            'message' => 'User created successfully',
            'user' => $user,
            'token' => $token,
        ], 201);
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

    public function sendResetCode(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        $code = Str::random(6); // Generate a 6-character code

        try {
            // Update or create the reset code entry in the database
            PasswordReset::updateOrCreate(
                ['email' => $request->email],
                ['code' => $code, 'created_at' => now()]
            );

            // Send code to user via email (or SMS)
            Mail::to($user->email)->send(new \App\Mail\PasswordResetCode($code));

            return response()->json(['success' => 'Reset code sent successfully!'], 200);
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            Log::error('Error sending reset code: ' . $e->getMessage());

            return response()->json(['error' => 'Failed to send reset code. Please try again later.'], 500);
        }
    }

    public function verifyResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required',
        ]);
    
        // Retrieve the reset record based on email and code
        $reset = PasswordReset::where('email', $request->email)
                               ->where('code', $request->code)
                               ->first();
    
        // Check if the reset record exists and if the code has not expired
        if (!$reset) {
            return response()->json(['error' => 'Invalid code.'], 400);
        }
    
        // Convert created_at to a Carbon instance if it's not already
        $createdAt = Carbon::parse($reset->created_at);
    
        // Check if the code is expired (60 minutes validity)
        if ($createdAt->diffInMinutes(now()) > 60) {
            return response()->json(['error' => 'Code expired.'], 400);
        }
    
        // Code is valid
        return response()->json(['success' => 'Code verified successfully!'], 200);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);
    
        $reset = PasswordReset::where('email', $request->email)
                               ->where('code', $request->code)
                               ->first();
        
        if (!$reset) {
            return response()->json(['error' => 'Invalid or expired code.'], 400);
        }
    
        $createdAt = Carbon::parse($reset->created_at);
        if ($createdAt->diffInMinutes(now()) > 60) {
            return response()->json(['error' => 'Invalid or expired code.'], 400);
        }
    
        $user = User::where('email', $request->email)->first();
        
        // Check if the new password is the same as the old password
        if (Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'New password cannot be the same as the current password.'], 400);
        }
    
        $user->password = Hash::make($request->password);
        $user->save();
    
        $reset->delete(); // Remove the code after successful reset
    
        return response()->json(['success' => 'Password reset successfully!'], 200);
    }
}