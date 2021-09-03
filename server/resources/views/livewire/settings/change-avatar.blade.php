<div style="margin: 1.5em 0;">
    @if (session()->has('change_avatar_message'))
        <div class="notification is-success">
            <button class="delete" onclick="this.parentNode.parentNode.removeChild(this.parentNode);"></button>
            <p>{{ session('change_avatar_message') }}</p>
        </div>
    @endif

    <form class="box" wire:submit.prevent="changeAvatar">
        <h2 class="title is-4">@lang('settings.change_avatar.header')</h2>

        @if (Auth::user()->avatar != null)
            <div class="field">
                <p>@lang('settings.change_avatar.has_avatar')</p>
            </div>

            <div class="box" style="background-color: #ccc; width: 50%;">
                <div style="background-image: url(/storage/avatars/{{ Auth::user()->avatar }}); background-size: cover; background-position: center center; padding-top: 100%;"></div>
            </div>
        @else
            <div class="field">
                <p><i>@lang('settings.change_avatar.no_avatar')</i></p>
            </div>
        @endif

        <div class="field">
            <label class="label" for="avatar">@lang('settings.change_avatar.avatar')</label>

            <div class="control">
                <input class="input @error('avatar') is-danger @enderror" type="file" accept=".jpg,.jpeg,.png" id="avatar"
                    wire:model="avatar" required>

                @error('avatar')
                    <p class="help is-danger">{{ $message }}</p>
                @else
                    <p class="help">@lang('settings.change_avatar.avatar_help')</p>
                @enderror
            </div>
        </div>

        <div class="field">
            <div class="control">
                <button class="button is-link" type="submit">@lang('settings.change_avatar.change_button')</button>
                @if (Auth::user()->avatar != null)
                    <a class="button is-danger" wire:click="deleteAvatar">@lang('settings.change_avatar.delete_button')</a>
                @endif
            </div>
        </div>
    </form>
</div>
