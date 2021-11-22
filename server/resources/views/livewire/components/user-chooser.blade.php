@if (!$inline)
<div class="field">
    <label class="label" for="userName">@lang('components.user_chooser.user')</label>
@endif
    <div class="dropdown @if($isOpen) is-active @endif control" style="width: 100%;">
        <div class="dropdown-trigger control has-icons-left" style="width: 100%;">
            <input class="input @if (!$isValid) is-danger @endif" type="text" placeholder="@lang($relationship ? 'components.user_chooser.search_by_user' : 'components.user_chooser.search_user')"
                wire:model="userName" id="userName" autocomplete="off" wire:keydown.enter.prevent="selectFirstUser"
                wire:focus="$set('isOpen', true)" wire:blur.debounce.100ms="$set('isOpen', false)">
            <span class="icon is-small is-left">
                <div class="image is-small is-round" style="background-image: url(/storage/avatars/{{ $user != null && $user->avatar != null ? $user->avatar : App\Models\Setting::get('default_user_avatar') }});"></div>
            </span>
        </div>
        <div class="dropdown-menu" style="width: 100%;">
            <div class="dropdown-content">
                @if ($filteredUsers->count() > 0)
                    @foreach ($filteredUsers as $user)
                        <a href="#" wire:click.prevent="selectUser({{ $user->id }})" class="dropdown-item">
                            <div class="image is-small is-round is-inline" style="background-image: url(/storage/avatars/{{ $user->avatar ?? App\Models\Setting::get('default_user_avatar') }});"></div>
                            {!! $userName != '' ? str_replace(' ', '&nbsp;', preg_replace('/(' . preg_quote($userName) . ')/i', '<b>$1</b>', $user->name)) : $user->name !!}
                        </a>
                    @endforeach
                @else
                    <div class="dropdown-item"><i>@lang('components.user_chooser.empty')</i></div>
                @endif
            </div>
        </div>
    </div>
@if (!$inline)
    @if (!$isValid) <p class="help is-danger">@lang('components.user_chooser.empty_error')</p> @endif
</div>
@endif
