<div>
    @if ($isChanged)
        <div class="notification is-success">
            <button class="delete" wire:click="$set('isChanged', false)"></button>
            <p>@lang('settings.change_avatar.success_message')</p>
        </div>
    @endif

    @if ($isDeleted)
        <div class="notification is-warning">
            <button class="delete" wire:click="$set('isDeleted', false)"></button>
            <p>@lang('settings.change_avatar.delete_message')</p>
        </div>
    @endif

    <form class="box" wire:submit.prevent="changeAvatar">
        <h2 class="title is-4">@lang('settings.change_avatar.header')</h2>

        @if (Auth::user()->avatar != null)
            <div class="field">
                <p>@lang('settings.change_avatar.has_avatar')</p>
            </div>

            <div class="box" style="width: 75%;">
                <div style="background-image: url(/storage/avatars/{{ Auth::user()->avatar }}); background-size: cover; background-position: center center; padding-top: 100%; border-radius: 6px;"></div>
            </div>
        @else
            <div class="field">
                <p>@lang('settings.change_avatar.no_avatar')</p>
            </div>

            <div class="box" style="width: 75%;">
                <div style="background-image: url(/storage/avatars/{{ App\Models\Setting::get('default_user_avatar') }}); background-size: cover; background-position: center center; padding-top: 100%; border-radius: 6px;"></div>
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
                    <a class="button is-danger" wire:click="deleteAvatar" wire:loading.attr="disabled">@lang('settings.change_avatar.delete_button')</a>
                @endif
            </div>
        </div>
    </form>
</div>
