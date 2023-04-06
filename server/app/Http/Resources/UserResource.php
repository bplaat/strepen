<?php

namespace App\Http\Resources;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'firstname' => $this->firstname,
            'insertion' => $this->insertion,
            'lastname' => $this->lastname,
            'avatar' => asset('/storage/avatars/'.($this->avatar ?? Setting::get('default_user_avatar'))),
            'thanks' => asset('/storage/thanks/'.($this->thanks ?? Setting::get('default_user_thanks'))),
            $this->mergeWhen($request->user()->manager || $this->id == $request->user()->id, [
                'gender' => match ($this->gender) {
                    User::GENDER_MALE => 'male',
                    User::GENDER_FEMALE => 'female',
                    User::GENDER_OTHER => 'other',
                },
                'birthday' => $this->birthday != null ? $this->birthday->format('Y-m-d') : null,
                'email' => $this->email,
                'phone' => $this->phone,
                'address' => $this->address,
                'postcode' => $this->postcode,
                'city' => $this->city,
                'role' => match ($this->role) {
                    User::ROLE_NORMAL => 'normal',
                    User::ROLE_MANAGER => 'manager',
                    User::ROLE_ADMIN => 'admin',
                },
                'lanuage' => match ($this->language) {
                    User::LANGUAGE_ENGLISH => 'en',
                    User::LANGUAGE_DUTCH => 'nl',
                },
                'theme' => match ($this->theme) {
                    User::THEME_LIGHT => 'light',
                    User::THEME_DARK => 'dark',
                },
                'receive_news' => $this->receive_news,
                'balance' => $this->balance,
                'minor' => $this->minor,
                'created_at' => $this->created_at,
            ]),
            $this->mergeWhen($request->user()->manager, [
                'active' => $this->active,
                'updated_at' => $this->updated_at,
            ]),
            'posts' => PostResource::collection($this->whenLoaded('posts')),
            'inventories' => InventoryResource::collection($this->whenLoaded('inventories')),
            'transactions' => TransactionResource::collection($this->whenLoaded('transactions')),
        ];
    }
}
