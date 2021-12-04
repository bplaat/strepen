<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Setting;
use Tests\TestCase;

class KioskTest extends TestCase
{
    // Test to go to kiosk page when whitelisted
    public function test_open_kiosk_on_whitelist()
    {
        Setting::set('kiosk_ip_whitelist', '127.0.0.1');
        $this->get(route('admin.kiosk'))->assertRedirect(route('kiosk'));
    }

    // Test to go to kiosk page when not whitelisted
    public function test_open_kiosk_not_on_whitelist()
    {
        Setting::set('kiosk_ip_whitelist', '');
        $this->get(route('admin.kiosk'))->assertStatus(403);
    }

    // Test to go to kiosk page wrong url whitelisted
    public function test_open_kiosk_direct_on_whitelist()
    {
        Setting::set('kiosk_ip_whitelist', '127.0.0.1');
        $this->get(route('kiosk'))->assertStatus(403);
    }

    // Test to go to kiosk page wrong url not whitelisted
    public function test_open_kiosk_direct_not_on_whitelist()
    {
        Setting::set('kiosk_ip_whitelist', '');
        $this->get(route('kiosk'))->assertStatus(403);
    }

    // Test kiosk / stripe page
    public function test_kiosk()
    {
        $this->actingAs(User::find(1));

        $this->get(route('kiosk'))->assertStatus(200);
    }
}
