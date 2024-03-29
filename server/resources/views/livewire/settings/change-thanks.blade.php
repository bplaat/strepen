<div>
    @if ($isChanged)
        <div class="notification is-success">
            <button class="delete" wire:click="$set('isChanged', false)"></button>
            <p>@lang('settings.change_thanks.success_message')</p>
        </div>
    @endif

    @if ($isDeleted)
        <div class="notification is-warning">
            <button class="delete" wire:click="$set('isDeleted', false)"></button>
            <p>@lang('settings.change_thanks.delete_message')</p>
        </div>
    @endif

    <form class="box" wire:submit.prevent="changeThanks">
        <h2 class="title is-4">@lang('settings.change_thanks.header')</h2>

        @if (Auth::user()->thanks != null)
            <div class="field">
                <p>@lang('settings.change_thanks.has_thanks')</p>
            </div>

            <div class="box" style="width: 75%;">
                <div class="image is-square is-rounded" style="background-image: url(/storage/thanks/{{ Auth::user()->thanks }});"></div>
            </div>
        @else
            <div class="field">
                <p>@lang('settings.change_thanks.no_thanks')</p>
            </div>

            <div class="box" style="width: 75%;">
                <div class="image is-square is-rounded" style="background-image: url(/storage/thanks/{{ App\Models\Setting::get('default_user_thanks') }});"></div>
            </div>
        @endif

        <div class="field">
            <label class="label" for="thanks">@lang('settings.change_thanks.thanks')</label>

            <div class="control">
                <input class="input @error('thanks') is-danger @enderror" type="file" accept=".gif" id="thanks"
                    wire:model="thanks" required>

                @error('thanks')
                    <p class="help is-danger">{{ $message }}</p>
                @else
                    <p class="help">@lang('settings.change_thanks.thanks_help')</p>
                @enderror
            </div>
        </div>

        <div class="field">
            <div class="control">
                <div class="buttons">
                    <button class="button is-link" type="submit">@lang('settings.change_thanks.change_button')</button>
                    @if (Auth::user()->thanks != null)
                        <a class="button is-danger" wire:click="deleteThanks" wire:loading.attr="disabled">@lang('settings.change_thanks.delete_button')</a>
                    @endif
                </div>
            </div>
        </div>
    </form>
</div>
