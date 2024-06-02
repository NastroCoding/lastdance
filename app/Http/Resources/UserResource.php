<?php

namespace App\Http\Resources;

use App\Models\Follow;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'username' => $this->username,
            'bio' => $this->bio,
            'is_private' => $this->is_private,
            'created_at' => $this->created_at,
            'is_your_account' => $request->user()->id == $this->id ? 'true' : 'false',
            'following_status' => Follow::where(['follower_id' =>  $request->user()->id, 'following_id' => $this->id])->first() ? 'following' : 'not-following',
            'posts_count' => $this->posts->count(),
            'followers_count' => $this->followers->count(),
            'following_count' => $this->followings->count(),
            'posts' => PostResource::collection($this->posts)
        ];
    }
}
