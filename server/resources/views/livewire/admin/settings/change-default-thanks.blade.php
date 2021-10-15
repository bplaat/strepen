<div>
    @if ($isChanged)
        <div class="notification is-success">
            <button class="delete" wire:click="$set('isChanged', false)"></button>
            <p>@lang('admin/settings.change_default_thanks.success_message')</p>
        </div>
    @endif

    @if ($isDeleted)
        <div class="notification is-warning">
            <button class="delete" wire:click="$set('isDeleted', false)"></button>
            <p>@lang('admin/settings.change_default_thanks.delete_message')</p>
        </div>
    @endif

    <form class="box" wire:submit.prevent="changeThanks">
        <h2 class="title is-4">@lang('admin/settings.change_default_thanks.header')</h2>

        @if (App\Models\Setting::get('default_user_thanks') != 'uV62yH12x12qE55fqcZVR2uGk0S1qiR1.gif')
            <div class="field">
                <p>@lang('admin/settings.change_default_thanks.has_thanks')</p>
            </div>

            <div class="box" style="width: 75%;">
                <div style="background-image: url(/storage/thanks/{{ App\Models\Setting::get('default_user_thanks') }}); background-size: cover; background-position: center center; padding-top: 100%; border-radius: 6px;"></div>
            </div>
        @else
            <div class="field">
                <p>@lang('admin/settings.change_default_thanks.no_thanks')</p>
            </div>

            <div class="box" style="width: 75%;">
                <div style="background-image: url(/storage/thanks/uV62yH12x12qE55fqcZVR2uGk0S1qiR1.gif); background-size: cover; background-position: center center; padding-top: 100%; border-radius: 6px;"></div>
            </div>
        @endif

        <div class="field">
            <label class="label" for="thanks">@lang('admin/settings.change_default_thanks.thanks')</label>

            <div class="control">
                <input class="input @error('thanks') is-danger @enderror" type="file" accept=".gif" id="thanks"
                    wire:model="thanks" required>

                @error('thanks')
                    <p class="help is-danger">{{ $message }}</p>
                @else
                    <p class="help">@lang('admin/settings.change_default_thanks.thanks_help')</p>
                @enderror
            </div>
        </div>

        <div class="field">
            <div class="control">
                <button class="button is-link" type="submit">@lang('admin/settings.change_default_thanks.change_button')</button>
                @if (App\Models\Setting::get('default_user_thanks') != 'uV62yH12x12qE55fqcZVR2uGk0S1qiR1.gif')
                    <a class="button is-danger" wire:click="deleteThanks" wire:loading.attr="disabled">@lang('admin/settings.change_default_thanks.delete_button')</a>
                @endif
            </div>
        </div>
    </form>
</div>
