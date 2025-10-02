<?php

namespace Tests\Feature;

use App\Http\LiveWire\Balance;
use App\Models\User;
use App\Models\Transaction;
use Tests\TestCase;
use Livewire;

class BalanceTest extends TestCase
{
    // Test balance page guest
    public function test_balance_guest()
    {
        $this->get(route('balance'))->assertRedirect(route('auth.login'));
    }

    // Test balance page authed
    public function test_balance_authed()
    {
        $user = User::factory()
            ->has(Transaction::factory()->count(25))
            ->create();
        $this->actingAs($user);

        $user->recalculateBalance();
        $user->update();

        Livewire::test(Balance::class)
            ->assertSee($user->balance);
    }
}
