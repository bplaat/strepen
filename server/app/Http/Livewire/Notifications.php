<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Auth;

class Notifications extends PaginationComponent
{
    public function render()
    {
        return view('livewire.notifications', [
            'notifications' => Auth::user()->notifications
                ->paginate(config('pagination.web.limit'))->withQueryString()
        ])->layout('layouts.app', ['title' => __('notifications.title')]);
    }
}
