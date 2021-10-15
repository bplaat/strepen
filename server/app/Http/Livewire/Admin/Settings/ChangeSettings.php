<?php

namespace App\Http\Livewire\Admin\Settings;

use App\Models\Setting;
use Livewire\Component;

class ChangeSettings extends Component
{
    public $minUserBalance;
    public $maxStripeAmount;
    public $minorAge;
    public $kioskIpWhitelist;
    public $isChanged = false;

    public $rules = [
        'minUserBalance' => 'required|numeric',
        'maxStripeAmount' => 'required|integer|min:1',
        'minorAge' => 'required|integer|min:1',
        'kioskIpWhitelist' => 'nullable|min:7',
    ];

    public function mount()
    {
        $this->minUserBalance = Setting::get('min_user_balance');
        $this->maxStripeAmount = Setting::get('max_stripe_amount');
        $this->minorAge = Setting::get('minor_age');
        $this->kioskIpWhitelist = Setting::get('kiosk_ip_whitelist');
    }

    public function changeDetails()
    {
        $this->validate();
        Setting::set('min_user_balance', $this->minUserBalance);
        Setting::set('max_stripe_amount', $this->maxStripeAmount);
        Setting::set('minor_age', $this->minorAge);
        Setting::set('kiosk_ip_whitelist', $this->kioskIpWhitelist);
        $this->isChanged = true;
    }

    public function render()
    {
        return view('livewire.admin.settings.change-settings');
    }
}
