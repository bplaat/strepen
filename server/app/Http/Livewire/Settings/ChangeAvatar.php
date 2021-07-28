<?php

namespace App\Http\Livewire\Settings;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class ChangeAvatar extends Component
{
    use WithFileUploads;

    public $avatar;

    public $rules = [
        'avatar' => 'image|max:1024'
    ];

    public function changeAvatar()
    {
        $this->validate();

        // Save file to avatars folder
        $avatarName = User::generateAvatarName($this->avatar->extension());
        $this->avatar->storeAs('public/avatars', $avatarName);

        // Delete old user avatar
        if (Auth::user()->avatar != null) {
            Storage::delete('public/avatars/' . Auth::user()->avatar);
        }

        // Update user that he has an avatar
        Auth::user()->update([ 'avatar' => $avatarName ]);

        session()->flash('change_avatar_message', __('settings.change_avatar.success_message'));
    }

    public function deleteAvatar()
    {
        // Delete user avatar file from storage
        Storage::delete('public/avatars/' . Auth::user()->avatar);

        // Update user that he has no avatar
        Auth::user()->update([ 'avatar' => null ]);

        session()->flash('change_avatar_message', __('settings.change_avatar.delete_message'));
    }

    public function render()
    {
        return view('livewire.settings.change-avatar');
    }
}
