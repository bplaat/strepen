<?php

namespace App\Http\Livewire\Admin\Settings;

use App\Models\Setting;
use Livewire\Component;

class ChangeSettings extends Component
{
    public $minUserBalance;
    public $maxStripeAmount;
    public $minorAge;
    public $paginationRows;
    public $kioskIpWhitelist;
    public $leaderboardsEnabled;
    public $bankAccountIban;
    public $bankAccountHolder;
    public $isChanged = false;

    public $rules = [
        'minUserBalance' => 'required|numeric',
        'maxStripeAmount' => 'required|integer|min:1',
        'minorAge' => 'required|integer|min:1',
        'paginationRows' => 'required|integer|min:1|max:10',
        'kioskIpWhitelist' => 'nullable|min:7',
        'leaderboardsEnabled' => 'nullable|boolean',
        'bankAccountIban' => 'nullable|min:8',
        'bankAccountHolder' => 'nullable|min:2'
    ];

    public function mount()
    {
        $this->minUserBalance = Setting::get('min_user_balance');
        $this->maxStripeAmount = Setting::get('max_stripe_amount');
        $this->minorAge = Setting::get('minor_age');
        $this->paginationRows = Setting::get('pagination_rows');
        $this->kioskIpWhitelist = Setting::get('kiosk_ip_whitelist');
        $this->leaderboardsEnabled = Setting::get('leaderboards_enabled') == 'true';
        $this->bankAccountIban = Setting::get('bank_account_iban');
        $this->bankAccountHolder = Setting::get('bank_account_holder');
    }

    public function changeDetails()
    {
        $this->validate();
        Setting::set('min_user_balance', $this->minUserBalance);
        Setting::set('max_stripe_amount', $this->maxStripeAmount);
        Setting::set('minor_age', $this->minorAge);
        Setting::set('pagination_rows', $this->paginationRows);
        Setting::set('kiosk_ip_whitelist', $this->kioskIpWhitelist);
        Setting::set('leaderboards_enabled', $this->leaderboardsEnabled == true ? 'true' : 'false');
        Setting::set('bank_account_iban', $this->bankAccountIban);
        Setting::set('bank_account_holder', $this->bankAccountHolder);
        $this->isChanged = true;
    }

    public function render()
    {
        return view('livewire.admin.settings.change-settings');
    }
}
