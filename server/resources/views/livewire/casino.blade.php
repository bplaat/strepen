<div class="container">
    <h2 class="title">@lang('casino.header')</h2>
    <p>Work in progress...</p>

    <p class="title has-text-centered mb-5">Current pot: &euro; 10,30</p>

    <!-- https://jamesrwilliams.ca/posts/css-wheel-of-fortune -->
    <div style="width: 40%; margin: 0 auto 3rem;">
        <div style="padding-top: 100%; background-color: #f00; border-radius: 50%;">
        </div>
    </div>

    @if (Auth::id() == 1)
        <livewire:components.user-chooser name="user" sortBy="last_transaction" />
    @endif

    <div class="field">
        <div class="control">
            <button type="submit" class="button is-link is-fullwidth p-5" wire:loading.attr="disabled">@lang('casino.spin') (&euro; 1,-)</button>
        </div>
    </div>
</div>
