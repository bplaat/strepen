<?php

namespace Tests\Feature;

use App\Http\LiveWire\Notifications;
use App\Models\User;
use App\Models\Transaction;
use Tests\TestCase;
use Livewire;

class NotificationsTest extends TestCase
{
    // Test notifications page guest
    public function test_notifications_guest()
    {
        $this->get(route('notifications'))->assertRedirect(route('auth.login'));
    }

    // Test notifications see notification
    public function test_notifications_see_notification()
    {
        $user = User::factory()
            ->has(Transaction::factory()->count(25))
            ->create();
        $this->actingAs($user);

        $user->recalculateBalance();
        $user->update();
        User::checkBalances();

        Livewire::test(Notifications::class)
            ->assertSee('Balans te laag');
    }
}
