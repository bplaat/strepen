<?php

namespace App\Http\Livewire;

use App\Models\Transaction;
use App\Models\Setting;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Casino extends Component
{
    public $spinPrice;
    public $spinPot;

    public function mount()
    {
        $this->spinPrice = Setting::get('casino_spin_price');
        $this->spinPot = Setting::get('casino_spin_pot');
    }

    public function spin()
    {
        // Create spin payment
        $this->transaction = new Transaction();
        $this->transaction->type = Transaction::TYPE_PAYMENT;
        $this->transaction->user_id = Auth::id();
        $this->transaction->name = 'Spin on the wheel of fortune in the casino on ' . date('Y-m-d H:i:s');
        $this->transaction->price = 1;
        $this->transaction->save();

        // Recalculate balance of user
        $user = User::find($this->transaction->user_id);
        $user->balance -= $this->transaction->price;
        $user->save();

        // Add spin payment to spin p
        $this->spinPot += $this->spinPrice;

        // TODO
        // if (rand(1, 32) == 1) {
        //     // bier 24

        //     // bier 12

        //     // bier 6

        // if (rand(1, 16) == 1) {
        //     // bier 2

        // soda 2

        // choco 2

        // chips 4

        // if (rand(1, 4) == 1) {
        //     // bier / soda

        //     if (rand(1, 4) == 1) {
        //         // choco
        //     } else {
        //         if (rand(1, 2) == 1) {
        //             // chips
        //         } else {

        //         }
        //     }

        // Save new spin pot
        Setting::set('casino_spin_pot', $this->spinPot);
    }

    public function render()
    {
        return view('livewire.casino')->layout('layouts.app', ['title' => __('casino.title')]);
    }
}
