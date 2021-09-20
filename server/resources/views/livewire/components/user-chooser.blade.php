<div class="field">
    <label class="label" for="userName">@lang('components.user_chooser.user')</label>
    <div class="dropdown @if($isOpen) is-active @endif" style="width: 100%;">
        <div class="dropdown-trigger control has-icons-left" style="width: 100%;">
            <input class="input" type="text" placeholder="@lang('components.user_chooser.search_user')"
                wire:model="userName" id="userName" autocomplete="off" wire:keydown.enter.prevent="selectFirstUser"
                wire:focus="$set('isOpen', true)" wire:blur="$set('isOpen', false)">
            <span class="icon is-small is-left">
                <div style="width: 24px; height: 24px; border-radius: 50%; background-size: cover; background-position: center center;
                    background-image: url({{ $user != null && $user->avatar != null ? '/storage/avatars/' . $user->avatar : '/images/avatars/mp.jpg' }});"></div>
            </span>
        </div>
        <div class="dropdown-menu" style="width: 100%;">
            <div class="dropdown-content">
                @foreach ($filteredUsers as $user)
                    <a href="#" wire:click.prevent="selectUser({{ $user->id }})" class="dropdown-item" style="display: flex; align-items: center;">
                        <div style="margin-right: .75rem; width: 24px; height: 24px; border-radius: 50%; background-size: cover; background-position: center center;
                            background-image: url({{ $user->avatar != null ? '/storage/avatars/' . $user->avatar : '/images/avatars/mp.jpg' }});"></div>
                        {!! $userName != '' ? str_replace(' ', '&nbsp;', preg_replace('/(' . preg_quote($userName) . ')/i', '<b>$1</b>', $user->name)) : $user->name !!}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
