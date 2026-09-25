<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'=>'required|email',
            'password'=>'required'
        ]);
        if(!Auth::attempt($request->only('email','password'))){
            return response()->json([
                'success'=>false,
                'message'=>'Email atau Password salah'
            ],404);
        }
        $user=Auth::user();
        $token=$user->createToken('flutter')->plainTextToken;
        return response()->json([
            'succsess'=>true,
            'message'=>'Login berhasil',
            'token'=>$token,
            'user'=>$user
        ]);
    }
   public function logout(Request $request)
{
    /** @var \App\Models\User $user */
    $user = $request->user();
    $user->currentAccessToken()->delete();

    return response()->json([
        'success' => true,
        'message' => 'Logout berhasil'
    ]);
}
}
