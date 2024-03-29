<div>
    @if ($isChanged)
        <div class="notification is-success">
            <button class="delete" wire:click="$set('isChanged', false)"></button>
            <p>@lang('admin/settings.change_default_avatar.success_message')</p>
        </div>
    @endif

    @if ($isDeleted)
        <div class="notification is-warning">
            <button class="delete" wire:click="$set('isDeleted', false)"></button>
            <p>@lang('admin/settings.change_default_avatar.delete_message')</p>
        </div>
    @endif

    <form class="box" wire:submit.prevent="changeAvatar">
        <h2 class="title is-4">@lang('admin/settings.change_default_avatar.header')</h2>

        @if (App\Models\Setting::get('default_user_avatar') != 'default.png')
            <div class="field">
                <p>@lang('admin/settings.change_default_avatar.has_avatar')</p>
            </div>

            <div class="box" style="width: 75%;">
                <div class="image is-square is-rounded" style="background-image: url(/storage/avatars/{{ App\Models\Setting::get('default_user_avatar') }});"></div>
            </div>
        @else
            <div class="field">
                <p>@lang('admin/settings.change_default_avatar.no_avatar')</p>
            </div>

            <div class="box" style="width: 75%;">
                <div class="image is-square is-rounded" style="background-image: url(/storage/avatars/default.png);"></div>
            </div>
        @endif

        <div class="field">
            <label class="label" for="avatar">@lang('admin/settings.change_default_avatar.avatar')</label>

            <div class="control">
                <input class="input @error('avatar') is-danger @enderror" type="file" accept=".jpg,.jpeg,.png" id="avatar"
                    wire:model="avatar" required>

                @error('avatar')
                    <p class="help is-danger">{{ $message }}</p>
                @else
                    <p class="help">@lang('admin/settings.change_default_avatar.avatar_help')</p>
                @enderror
            </div>
        </div>

        <div class="field">
            <div class="control">
                <div class="buttons">
                    <button class="button is-link" type="submit">@lang('admin/settings.change_default_avatar.change_button')</button>
                    @if (App\Models\Setting::get('default_user_avatar') != 'default.png')
                        <a class="button is-danger" wire:click="deleteAvatar" wire:loading.attr="disabled">@lang('admin/settings.change_default_avatar.delete_button')</a>
                    @endif
                </div>
            </div>
        </div>
    </form>
</div>
