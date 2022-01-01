<?php

namespace App\Http\Resources;

use App\Models\Transaction;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray($request)
    {
        $type = 'unkown';
        if ($this->type == Transaction::TYPE_TRANSACTION) {
            $type = 'transaction';
        }
        if ($this->type == Transaction::TYPE_DEPOSIT) {
            $type = 'deposit';
        }
        if ($this->type == Transaction::TYPE_FOOD) {
            $type = 'food';
        }

        return [
            'id' => $this->id,
            'type' => $type,
            'name' => $this->name,
            'price' => $this->price,
            'created_at' => $this->created_at,
            'updated_at' => $this->when($request->user()->manager, $this->updated_at),
            'user' => new UserResource($this->whenLoaded('user')),
            'products' => ProductResource::collection($this->whenLoaded('products'))
        ];
    }
}
