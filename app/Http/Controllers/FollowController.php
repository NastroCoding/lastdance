<?php

namespace App\Http\Controllers;

use App\Http\Resources\FollowResource;
use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function follow(Request $request, $username) {
        $my_id = $request->user()->id;
        $user = User::where('username', $username)->first();
        
        if (!$user) {
            return response()->json([
                'messages' => 'User not found'
            ], 404);
        }
        
        $user_id = $user->id;
        
        if($my_id == $user_id){
            return response()->json([
                'message' => 'You are not allowed to follow yourself'
            ], 422);
        }

        $check = Follow::where([
            'follower_id' => $my_id,
            'following_id' => $user_id
        ])->first();

        if ($check) {
            return response()->json([
                'message' => 'You are already followed',
                'status' => 'following'
            ], 422);
        }

        $follow = Follow::create([
            'follower_id' => $my_id,
            'following_id' => $user_id,
            'is_accepted' => true
        ]);

        return response()->json([
            'message' => 'Follow success',
            'status' => 'following'
        ], 200);
    }

    public function unfollow(Request $request, $username){
        $my_id = $request->user()->id;
        $user = User::where('username', $username)->first();
        
        if (!$user) {
            return response()->json([
                'messages' => 'User not found'
            ], 404);
        }
        
        $user_id = $user->id;
        
        if($my_id == $user_id){
            return response()->json([
                'message' => 'You are not allowed to unfollow yourself'
            ], 422);
        }

        $follow = Follow::where([
            'follower_id' => $my_id,
            'following_id' => $user_id
        ])->first();

        if (!$follow) {
            return response()->json([
                'message' => 'You are not following the user',
            ]);
        }

        $follow->delete();
    }

    public function following($username) {
        $user = User::where('username', $username)->first();
        if(!$user){
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        $user_id = $user->id;
        $follow = $user->followings;
        return response()->json([
            'following' => FollowResource::collection($follow)
        ], 200);
    }

    public function followers($username){
        $user = User::where('username', $username)->first();
        if(!$user){
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        $user_id = $user->id;
        $follow = $user->followers;
        return response()->json([
            'following' => FollowResource::collection($follow)
        ], 200);
    }
}
