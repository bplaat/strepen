<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model {
    // Turn model to api data
    public static function forApi($notification, $user)
    {
        if ($notification->type == 'App\\Notifications\\NewDeposit') $notification->type = 'new_deposit';
        if ($notification->type == 'App\\Notifications\\NewPost') $notification->type = 'new_post';
        if ($notification->type == 'App\\Notifications\\LowBalance') $notification->type = 'low_balance';

        $notification->user_id = $notification->notifiable_id;
        unset($notification->notifiable_type);
        unset($notification->notifiable_id);

        if ($user == null || $user->role != User::ROLE_ADMIN) {
            unset($notification->updated_at);
        }
    }
}
