<div>
    @if (session()->has('change_password_message'))
        <div class="notification is-success">
            <button class="delete" onclick="this.parentNode.parentNode.removeChild(this.parentNode);"></button>
            <p>{{ session('change_password_message') }}</p>
        </div>
    @endif

    <form class="box" wire:submit.prevent="changePassword">
        <h2 class="title is-4">@lang('settings.change_password.header')</h2>

        <div class="field">
            <label class="label" for="currentPassword">@lang('settings.change_password.current_password')</label>
            <div class="control">
                <input class="input @error('currentPassword') is-danger @enderror" type="password" id="currentPassword"
                    wire:model.defer="currentPassword" required>
            </div>
            @error('currentPassword') <p class="help is-danger">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <label class="label" for="password">@lang('settings.change_password.password')</label>
            <div class="control">
                <input class="input @error('password') is-danger @enderror" type="password" id="password"
                    wire:model.defer="password" required>
            </div>
            @error('password') <p class="help is-danger">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <label class="label" for="passwordConfirmation">@lang('settings.change_password.password_confirmation')</label>
            <div class="control">
                <input class="input @error('password') is-danger @enderror" type="password" id="passwordConfirmation"
                    wire:model.defer="passwordConfirmation" required>
            </div>
        </div>

        <div class="field">
            <div class="control">
                <button class="button is-link" type="submit">@lang('settings.change_password.button')</button>
            </div>
        </div>
    </form>
</div>
