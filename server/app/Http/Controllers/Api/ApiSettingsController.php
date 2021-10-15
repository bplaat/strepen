<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;

class ApiSettingsController extends Controller
{
    // API settings index route
    public function index()
    {
        return [
            'min_user_balance' => (float)Setting::get('min_user_balance'),
            'max_stripe_amount' => (float)Setting::get('max_stripe_amount'),
            'default_user_avatar' => asset('/storage/avatars/' . Setting::get('default_user_avatar')),
            'default_user_thanks' => asset('/storage/thanks/' . Setting::get('default_user_thanks')),
            'default_product_image' => asset('/storage/products/' . Setting::get('default_product_image'))
        ];
    }
}
