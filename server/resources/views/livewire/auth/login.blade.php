<form class="container" wire:submit.prevent="login">
    <h2 class="title is-4">@lang('auth.login.login')</h2>

    <div class="field">
        <label class="label" for="email">@lang('auth.login.email')</label>
        <div class="control">
            <input class="input @error('email') is-danger @enderror" type="email" id="email"
                wire:model.defer="email" autofocus required>
        </div>
        @error('email') <p class="help is-danger">{{ $message }}</p> @enderror
    </div>

    <div class="field">
        <label class="label" for="password">@lang('auth.login.password')</label>
        <div class="control">
            <input class="input @error('password') is-danger @enderror" type="password" id="password"
                wire:model.defer="password" required>
        </div>
        @error('password') @if ($message != 'null') <p class="help is-danger">{{ $message }}</p> @endif @enderror
    </div>

    <div class="field">
        <div class="control">
            <button class="button is-link" type="submit">@lang('auth.login.login')</button>
        </div>
    </div>
</form>
