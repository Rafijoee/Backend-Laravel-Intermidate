<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use function Laravel\Prompts\error;

class AuthController extends Controller
{
    public function register (Request $request) {
        try{
            $validation = $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|confirmed'
            ]);
            $validated['password'] = Hash::make($validation['password']);
    
            $user = User::create($validation);
            return response()->json([
                'message' => 'User registered successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ]
            ], 201);
        }catch(\Exception $e){
            return response()->json([
                'message' => 'User registration failed',
                'error' => $e
            ], 400);
        }
    }

    public function login(Request $request)
    {
        try {
            $validation = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
    
            $user = User::where('email', $validation['email'])->first();
    
            if (!$user || !Hash::check($validation['password'], $user->password)) {
                return response(['message' => 'Invalid credentials'], 401);
            }
    
            // Hapus token lama (opsional)
            $user->tokens()->delete();
    
            // Buat token baru
            $token = $user->createToken('api-token')->plainTextToken;
    
            // Set kedaluwarsa token baru
            $user->tokens()->latest()->first()->update([
                'expires_at' => now()->addMinutes(30),
            ]);
    
            return response()->json([
                "user" => $user,
                "token" => $token,
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Login failed', 'error' => $e->getMessage()], 400);
        }
    }

    public function logout (Request $request){
        try{
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                "message" => "Logged out"
            ]);
        }catch(\Exception $e){
            return response()->json([
                'message' => 'User logout failed',
                'error' => $e
            ], 400);
        }
    }

    public function tes(){
        return response()->json([
            "message" => "tes"
        ]);
    }
}
