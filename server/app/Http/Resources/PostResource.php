<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Parsedown;

class PostResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'image' => $this->image != null ? asset('/storage/posts/' . $this->image) : null,
            'body' => (new Parsedown())->text($this->body),
            'created_at' => $this->created_at,
            'updated_at' => $this->when($request->user()->manager, $this->updated_at),
            'user' => new UserResource($this->whenLoaded('user'))
        ];
    }
}
