<?php

namespace App\Http\Controllers\api;

use Exception;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function Laravel\Prompts\error;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register (Request $request) {
        try{
            DB::beginTransaction();

            $validation = $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|confirmed'
            ]);
            $validated['password'] = Hash::make($validation['password']);
    
            $user = User::create($validation);

            $actifyAccount = Str::random(60);
            
            $user->remember_token = $actifyAccount;
            $user->save();

            $activationLink = config('app.url') . "/api/actify/{$actifyAccount}";
            try {
                Mail::send(
                    'mail.actifyaccount',
                    ['activationLink' => $activationLink, 'name' => $user->name],
                    function ($message) use ($user) {
                        $message->to($user->email, $user->name)->subject('Actify Account');
                    }
                );
            } catch (\Exception $mailException) {
                // Hapus user jika email gagal dikirim
                $user->delete();
    
                throw new \Exception("Email gagal dikirim: " . $mailException->getMessage());
            }

            DB::commit();
            return response()->json([
                'message' => 'User registered successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'token' => $actifyAccount
                ]
            ], 201);
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'message' => 'User registration failed',
                'error' => $e->getMessage(), 'ini errorrnyaa'
            ], 400);
        }
    }

    public function actifyAccount ($token){
        $user = User::where('remember_token', $token)->first();
        if (!$user) {
            return response()->json([
                'message' => 'Invalid token'
            ], 400);
        }else{
            $user->is_active = true;
            $user->save();
            return response()->json([
                'message' => 'Account activated successfully'
            ], 200);
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
