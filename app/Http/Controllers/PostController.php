<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\PostAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $vld = Validator::make($request->all(), [
            'page' => 'min:0|integer',
            'size' => 'min:1|integer'
        ]);

        if($vld->fails()) {
            return response()->json([
                'message' => 'Invalid field',
                'errors' => $vld->messages()
            ]);
        }

        $size = 10;
        $request->get('size') ? $size = $request->get('size') : $size = 10;
        $post = Post::paginate($size);
        return response()->json([
            'size' => $post->perPage(),
            'page' => $post->currentPage(),
            'posts' => PostResource::collection($post)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $vld = Validator::make($request->all(), [
            'caption' => 'required',
            'attachments' => 'required|array',
            'attachments.*' => 'mimes:png,jpg,jpeg,webp,gif'
        ]);

        if($vld->fails()){
            return response()->json([
                'message' => 'Invalid field',
                'errors' => $vld->messages()
            ], 422);
        }

        $user = $request->user();
        $attachments = $request->attachments;

        $post = Post::create([
            'caption' => $request->caption,
            'user_id' => $user->id
        ]);

        foreach($attachments as $attachment) {
            $path = $attachment->storeAs('attachments', $attachment->getClientOriginalName());
            PostAttachment::create([
                'storage_path' => $path,
                'post_id' => $post->id
            ]);
        }

        return response()->json([
            'message' => 'Create post success',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $post = Post::find($id);
        if(!$post){
            return response()->json([
                'message' => 'Post not found'
            ], 404);
        }
        if(!$post->user_id == $user->id){
            return response()->json([
                'message' => 'Forbidden access'
            ], 403);
        }
        $post->delete();
        
    }
}
