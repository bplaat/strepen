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
            'casino_spin_price' => (float)Setting::get('casino_spin_price'),
            'casino_spin_pot' => (float)Setting::get('casino_spin_pot'),
            'bank_account_iban' => Setting::get('bank_account_iban'),
            'bank_account_holder' => Setting::get('bank_account_holder'),
            'product_beer_id' => (int)Setting::get('product_beer_id'),
            'product_soda_id' => (int)Setting::get('product_soda_id'),
            'product_candybar_id' => (int)Setting::get('product_candybar_id'),
            'product_chips_id' => (int)Setting::get('product_chips_id'),
            'default_user_avatar' => asset('/storage/avatars/' . Setting::get('default_user_avatar')),
            'default_user_thanks' => asset('/storage/thanks/' . Setting::get('default_user_thanks')),
            'default_product_image' => asset('/storage/products/' . Setting::get('default_product_image'))
        ];
    }
}
