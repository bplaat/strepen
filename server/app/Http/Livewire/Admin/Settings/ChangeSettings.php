<?php

namespace App\Http\Livewire\Admin\Settings;

use App\Models\Setting;
use Livewire\Component;

class ChangeSettings extends Component
{
    public $currencySymbol;
    public $currencyName;
    public $minUserBalance;
    public $maxStripeAmount;
    public $minorAge;
    public $paginationRows;
    public $kioskIpWhitelist;
    public $leaderboardsEnabled;
    public $casinoEnabled;
    public $bankAccountIban;
    public $bankAccountHolder;
    public $isChanged = false;

    public $rules = [
        'currencySymbol' => 'required|min:1|max:1',
        'currencyName' => 'required|min:2|max:24',
        'minUserBalance' => 'required|numeric',
        'maxStripeAmount' => 'required|integer|min:1',
        'minorAge' => 'required|integer|min:1',
        'paginationRows' => 'required|integer|min:1|max:10',
        'kioskIpWhitelist' => 'nullable|min:7|max:255',
        'leaderboardsEnabled' => 'nullable|boolean',
        'casinoEnabled' => 'nullable|boolean',
        'bankAccountIban' => 'nullable|min:8|max:48',
        'bankAccountHolder' => 'nullable|min:2|max:48'
    ];

    public $listeners = ['inputValue'];

    public function mount()
    {
        $this->currencySymbol = Setting::get('currency_symbol');
        $this->currencyName = Setting::get('currency_name');
        $this->minUserBalance = Setting::get('min_user_balance');
        $this->maxStripeAmount = Setting::get('max_stripe_amount');
        $this->minorAge = Setting::get('minor_age');
        $this->paginationRows = Setting::get('pagination_rows');
        $this->kioskIpWhitelist = Setting::get('kiosk_ip_whitelist');
        $this->leaderboardsEnabled = Setting::get('leaderboards_enabled') == 'true';
        $this->casinoEnabled = Setting::get('casino_enabled') == 'true';
        $this->bankAccountIban = Setting::get('bank_account_iban');
        $this->bankAccountHolder = Setting::get('bank_account_holder');
        $this->productBeerId = Setting::get('product_beer_id');
        $this->productSodaId = Setting::get('product_soda_id');
        $this->productCandybarId = Setting::get('product_candybar_id');
        $this->productChipsId = Setting::get('product_chips_id');
    }

    public function inputValue($name, $value)
    {
        if ($name == 'product_beer') {
            $this->productBeerId = $value;
        }
        if ($name == 'product_soda') {
            $this->productSodaId = $value;
        }
        if ($name == 'product_candybar') {
            $this->productCandybarId = $value;
        }
        if ($name == 'product_chips') {
            $this->productChipsId = $value;
        }
    }

    public function changeDetails()
    {
        $this->validate();
        Setting::set('currency_symbol', $this->currencySymbol);
        Setting::set('currency_name', $this->currencyName);
        Setting::set('min_user_balance', $this->minUserBalance);
        Setting::set('max_stripe_amount', $this->maxStripeAmount);
        Setting::set('minor_age', $this->minorAge);
        Setting::set('pagination_rows', $this->paginationRows);
        Setting::set('kiosk_ip_whitelist', $this->kioskIpWhitelist);
        Setting::set('leaderboards_enabled', $this->leaderboardsEnabled == true ? 'true' : 'false');
        Setting::set('casino_enabled', $this->casinoEnabled == true ? 'true' : 'false');
        Setting::set('bank_account_iban', $this->bankAccountIban);
        Setting::set('bank_account_holder', $this->bankAccountHolder);
        Setting::set('product_beer_id', $this->productBeerId);
        Setting::set('product_soda_id', $this->productSodaId);
        Setting::set('product_candybar_id', $this->productCandybarId);
        Setting::set('product_chips_id', $this->productChipsId);
        $this->isChanged = true;
    }

    public function render()
    {
        return view('livewire.admin.settings.change-settings');
    }
}
