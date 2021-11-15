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
    public $isChanged = false;

    public $rules = [
        'minUserBalance' => 'required|numeric',
        'maxStripeAmount' => 'required|integer|min:1',
        'minorAge' => 'required|integer|min:1',
        'paginationRows' => 'required|integer|min:1|max:10',
        'kioskIpWhitelist' => 'nullable|min:7',
        'leaderboardsEnabled' => 'nullable|boolean'
    ];

    public function mount()
    {
        $this->minUserBalance = Setting::get('min_user_balance');
        $this->maxStripeAmount = Setting::get('max_stripe_amount');
        $this->minorAge = Setting::get('minor_age');
        $this->paginationRows = Setting::get('pagination_rows');
        $this->kioskIpWhitelist = Setting::get('kiosk_ip_whitelist');
        $this->leaderboardsEnabled = Setting::get('leaderboards_enabled') == 'true';
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
        $this->isChanged = true;
    }

    public function render()
    {
        return view('livewire.admin.settings.change-settings');
    }
}
