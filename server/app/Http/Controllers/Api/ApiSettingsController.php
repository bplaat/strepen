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
            'currency_symbol' => Setting::get('currency_symbol'),
            'currency_name' => Setting::get('currency_name'),
            'min_user_balance' => (float)Setting::get('min_user_balance'),
            'max_stripe_amount' => (int)Setting::get('max_stripe_amount'),
            'minor_age' => (int)Setting::get('minor_age'),
            'pagination_rows' => (int)Setting::get('pagination_rows'),
            'leaderboards_enabled' => Setting::get('leaderboards_enabled') == 'true',
            'casino_enabled' => Setting::get('casino_enabled') == 'true',
            'bank_account_iban' => Setting::get('bank_account_iban'),
            'bank_account_holder' => Setting::get('bank_account_holder'),
            'default_user_avatar' => asset('/storage/avatars/' . Setting::get('default_user_avatar')),
            'default_user_thanks' => asset('/storage/thanks/' . Setting::get('default_user_thanks')),
            'default_product_image' => asset('/storage/products/' . Setting::get('default_product_image'))
        ];
    }
}
