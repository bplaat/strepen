<?php

namespace App\Http\Livewire\Components;

use App\Models\Notification;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Notifications extends Component
{
    public $isDark;

    public function mount()
    {
        $this->isDark = Auth::user()->theme == \App\Models\User::THEME_DARK;
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
        return view('livewire.components.notifications', [
            'notifications' => Auth::user()->unreadNotifications->slice(0, 5)
        ]);
    }
}
