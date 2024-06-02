<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request){
        $vld = Validator::make($request->all(), [
            'full_name' => 'required',
            'username' => 'required|unique:users,username',
            'bio' => 'required|max:100',
            'password' => 'required|min:6',
            'is_private' => 'boolean'
        ]);

        if($vld->fails()){
            return response()->json([
                'message' => 'Invalid field',
                'errors' => $vld->messages()
            ], 422);
        }
 
        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'bio' => $request->bio,
            'full_name' => $request->full_name,
            'is_private' => false
        ]);

        $token = $user->createToken('myToken')->plainTextToken;

        return response()->json([
            'message' => 'Register Success',
            'token' => $token,
            'user' => $user
        ], 201);
    }

    public function login(Request $request){
        $vld = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required'
        ]); 

        if($vld->fails()) {
            return response()->json([
                'message' => 'Invalid field',
                'error' => $vld->messages()
            ], 422);
        }

        $user = User::where('username', $request->username)->first();
        if(!$user || ! Hash::check($request->password, $user->password)){
            return response()->json([
                'message' => 'Wrong username or password'
            ], 401);
        }

        $token = $user->createToken('myToken')->plainTextToken;

        return response()->json([
            'message' => 'Login success',
            'token' => $token,
            'user' => $user
        ], 200);
    }

    public function logout(Request $request){
        $user = $request->user();
        $user->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logout Success'
        ], 200);
    }
}
