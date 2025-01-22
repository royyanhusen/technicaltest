<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\RefreshToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Register a new user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'message' => $validator->errors()], 400);
        }

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        // Create JWT token after registration
        $token = JWTAuth::fromUser($user);

        // Create a refresh token manually (set the expiration to 7 days)
        $refreshToken = JWTAuth::fromUser($user, ['exp' => now()->addDays(7)->timestamp]);

        // Save the refresh token in the database
        RefreshToken::create([
            'user_id' => $user->id,
            'token' => $refreshToken,
            'expires_at' => Carbon::now()->addDays(7),
        ]);

        return response()->json(['user' => $user, 'token' => $token, 'refresh_token' => $refreshToken], 201);
    }

    /**
     * Login a user and return a JWT token
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }

            // Generate refresh token with a longer expiration (e.g., 7 days)
            $user = User::where('email', $request->email)->first();
            $refreshToken = JWTAuth::fromUser($user, ['exp' => now()->addDays(7)->timestamp]);

            // Save the refresh token in the database
            RefreshToken::create([
                'user_id' => $user->id,
                'token' => $refreshToken,
                'expires_at' => Carbon::now()->addDays(7),
            ]);

        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }

        return response()->json(compact('token', 'refresh_token'));
    }

    /**
     * Logout the user and invalidate the token
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        // Invalidate the access token
        JWTAuth::invalidate(JWTAuth::getToken());

        // Remove the refresh token from the database
        RefreshToken::where('user_id', $user->id)->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh the JWT token using refresh token
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function refresh(Request $request)
    {
        $refreshToken = $request->input('refresh_token'); // Get refresh token from request

        try {
            // Validate refresh token and find the associated user
            $storedRefreshToken = RefreshToken::where('token', $refreshToken)->first();
            if (!$storedRefreshToken || $storedRefreshToken->expires_at < Carbon::now()) {
                return response()->json(['error' => 'Invalid or expired refresh token'], 401);
            }

            // Attempt to refresh the token using the provided refresh token
            if (!$token = JWTAuth::refresh($refreshToken)) {
                return response()->json(['error' => 'Could not refresh token'], 500);
            }

        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not refresh token'], 500);
        }

        return response()->json(compact('token')); // Return new JWT token
    }

    /**
     * Update last activity timestamp (Optional)
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateLastActivity(Request $request)
    {
        // Update the last activity time in the session
        session(['last_activity' => time()]);

        return response()->json(['status' => 'success']);
    }
}