<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index() {
        $user = User::all();
        return response()->json([
            'users' => $user
        ], 200);
    }

    public function show ($username){
        $user = User::where('username', $username)->first();
        if(!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        return response()->json(new UserResource($user), 200);
    }
}
