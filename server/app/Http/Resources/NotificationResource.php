<?php

namespace App\Http\Resources;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => match ($this->type) {
                'App\Notifications\NewDeposit' => 'new_deposit',
                'App\Notifications\NewPost' => 'new_post',
                'App\Notifications\LowBalance' => 'low_balance',
            },
            'data' => $this->data,
            'read_at' => $this->read_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->when($request->user()->manager, $this->updated_at),
            $this->mergeWhen($this->notifiable_type == 'App\Models\User', [
                'user' => new UserResource(User::find($this->notifiable_id)),
            ]),
            $this->mergeWhen($this->type == 'App\Models\NewPost', [
                'post' => new PostResource(Post::withTrashed()->find($this->data['post_id'])),
            ]),
        ];
    }
}
