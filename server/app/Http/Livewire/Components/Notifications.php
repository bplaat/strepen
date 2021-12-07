<?php

namespace App\Http\Livewire\Components;

use App\Models\Notification;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Notifications extends Component
{
    public $isLight;

    public function mount()
    {
        $this->isLight = Auth::user()->theme == \App\Models\User::THEME_LIGHT;
    }

    public function readNotification($notificationId)
    {
        $notification = Notification::find($notificationId);
        if ($notification != null) {
            $notification->read_at = now();
            $notification->save();
        }
    }

    public function render()
    {
        unset(Auth::user()->unreadNotifications);
        return view('livewire.components.notifications', [
            'notifications' => Auth::user()->unreadNotifications->slice(0, 5)
        ]);
    }
}
