<?php

namespace App\Http\Livewire;

use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class Notifications extends PaginationComponent
{
    public function render()
    {
        return view('livewire.notifications', [
            'notifications' => Auth::user()->notifications()
                ->paginate(Setting::get('pagination_rows') * 3)->withQueryString()
        ])->layout('layouts.app', ['title' => __('notifications.title')]);
    }
}
