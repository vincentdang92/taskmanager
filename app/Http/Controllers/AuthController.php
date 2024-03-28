<?php

namespace App\Http\Controllers;

use App\Enums\TokenAbility;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:50',
            'password' => 'required|max:50'
        ]);
        if(!Auth::attempt($validated)){
            return response()->json([
                'success' => false,
                'message' => 'Login information invalid!'
            ],401);
        }
        $user = User::where('email', $validated['email'])->first();
        if(!$user){
            return response()->json([
                'success' => false,
                'message' => 'Not found user!'
            ],404);
        }
        $accessToken = $user->createToken('access_token', ['*'], now()->addHour() );
        $refreshToken = $user->createToken('refresh_token', ['*'], now()->addWeek());
        return response()->json([
            'success' => true,
            'access_token' => $accessToken->plainTextToken,
            'refresh_token' => $refreshToken->plainTextToken,
            'token_type' => 'Bearer'
        ]);
    }
    public function register (Request $request){
        $validated = $request->validate([
            'name' => 'required|max:255|min:5',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|min:5|confirmed'
        ]);
        $user = User::create($validated);
        return response()->json([
            'success' => true,
            'access_token' => $user->createToken('api_token')->plainTextToken,
            'token_type' => 'Bearer',
            'data' => $user
        ], 201);
    }
    public function refreshToken(Request $request)
    {
        $request->validate([
            'refresh_token' => 'required',
        ]);
        $refreshToken = $request->refresh_token;
        $refreshTokenModel = DB::table('personal_access_tokens')->where('token', hash('sha256', $refreshToken))->first();
        if (!$refreshTokenModel) {
            return response()->json(['message' => 'Invalid refresh token'], 401);
        }
        $user = User::find($refreshTokenModel->tokenable_id);
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        $expiryDateTime = Carbon::parse($refreshTokenModel->expires_at);

        if ($expiryDateTime->isPast()) {
            // Token has expired
            return response()->json(['message' => 'Refresh token has expired'], 401);
        }

        // Generate new access token
        $accessToken = $user->createToken('access_token', ['*'], now()->addHour() )->plainTextToken;

        return response()->json(['access_token' => $accessToken]);
    }
}
