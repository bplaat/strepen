<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Kiosk extends Component
{
    public function render()
    {
        return view('livewire.kiosk')
            ->layout('layouts.app', ['title' => __('kiosk.title')]);
    }
}
