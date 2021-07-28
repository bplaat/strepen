<?php

namespace App\Http\Livewire\Settings;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ChangeDetails extends Component
{
    public $user;

    public function rules()
    {
        return [
            'user.firstname' => 'required|min:2|max:48',
            'user.insertion' => 'nullable|max:16',
            'user.lastname' => 'required|min:2|max:48',
            'user.gender' => 'nullable|integer|digits_between:' . User::GENDER_MALE . ',' . User::GENDER_OTHER,
            'user.birthday' => 'nullable|date',
            'user.email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore(Auth::user()->email, 'email')
            ],
            'user.phone' => 'nullable|max:255',
            'user.address' => 'nullable|min:2|max:255',
            'user.postcode' => 'nullable|min:2|max:32',
            'user.city' => 'nullable|min:2|max:255'
        ];
    }

    public function mount()
    {
        $this->user = Auth::user();
    }

    public function changeDetails()
    {
        $this->validate();

        if ($this->user->gender == '') $this->user->gender = null;
        if ($this->user->birthday == '') $this->user->birthday = null;
        $this->user->save();

        session()->flash('change_details_message', __('settings.change_details.success_message'));
    }

    public function render()
    {
        return view('livewire.settings.change-details');
    }
}
