<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        return response()->json([
            'success' => true,
            'access_token' => $user->createToken('api_token')->plainTextToken,
            'refresh_token' => $user->createToken('refresh_token')->plainTextToken,
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
}
