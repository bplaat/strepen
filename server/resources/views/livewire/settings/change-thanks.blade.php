<div style="margin: 1.5em 0;">
    @if (session()->has('change_thanks_message'))
        <div class="notification is-success">
            <button class="delete" onclick="this.parentNode.parentNode.removeChild(this.parentNode);"></button>
            <p>{{ session('change_thanks_message') }}</p>
        </div>
    @endif

    <form class="box" wire:submit.prevent="changeThanks">
        <h2 class="title is-4">@lang('settings.change_thanks.header')</h2>

        @if (Auth::user()->thanks != null)
            <div class="field">
                <p>@lang('settings.change_thanks.has_thanks')</p>
            </div>

            <div class="box" style="width: 50%;">
                <div style="background-image: url(/storage/thanks/{{ Auth::user()->thanks }}); background-size: cover; background-position: center center; padding-top: 100%;"></div>
            </div>
        @else
            <div class="field">
                <p>@lang('settings.change_thanks.no_thanks')</p>
            </div>

            <div class="box" style="width: 50%;">
                <div style="background-image: url(/images/thanks/default.gif); background-size: cover; background-position: center center; padding-top: 100%;"></div>
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
                <button class="button is-link" type="submit">@lang('settings.change_thanks.change_button')</button>
                @if (Auth::user()->thanks != null)
                    <a class="button is-danger" wire:click="deleteThanks" wire:loading.attr="disabled">@lang('settings.change_thanks.delete_button')</a>
                @endif
            </div>
        </div>
    </form>
</div>
