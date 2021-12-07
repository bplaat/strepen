<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    // Convert notification to API data
    public static function toApiData($notification, $forUser = null, $includes = [])
    {
        $data = new \stdClass();
        $data->id = $notification->id;

        if ($notification->type == 'App\\Notifications\\NewDeposit') {
            $data->type = 'new_deposit';
        }
        if ($notification->type == 'App\\Notifications\\NewPost') {
            $data->type = 'new_post';
        }
        if ($notification->type == 'App\\Notifications\\LowBalance') {
            $data->type = 'low_balance';
        }

        $data->data = $notification->data;
        $data->read_at = $notification->read_at;
        $data->created_at = $notification->created_at;

        if ($forUser != null && ($forUser->role == User::ROLE_MANAGER || $forUser->role == User::ROLE_ADMIN)) {
            $data->updated_at = $notification->updated_at;
        }

        if ($notification->notifiable_type == 'App\Models\User' && in_array('user', $includes)) {
            $data->user = User::find($notification->notifiable_id)->toApiData($forUser);
        }

        if ($notification->type == 'App\\Notifications\\NewPost' && in_array('post', $includes)) {
            $data->post = Post::find($notification->data->post_id)->toApiData($forUser);
        }

        return $data;
    }
}
