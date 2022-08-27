<div class="container">
    <h2 class="title">@lang('casino.header')</h2>

    <form class="box" wire:submit.prevent="spin">
        <h2 class="title is-4 mb-3">@lang('casino.spin.header')</h2>
        <p class="mb-5"><i>@lang('casino.spin.description')</i></p>

        <p class="title has-text-centered mb-5">@lang('casino.spin.current_pot'): <x-money-format :money="$spinPot" /></p>

        <!-- https://jamesrwilliams.ca/posts/css-wheel-of-fortune -->
        <div class="my-4">
            <div style="width: 40%; margin: 0 auto;">
                <div style="padding-top: 100%; background-color: #f00; border-radius: 50%;">
                </div>
            </div>
        </div>

        @if (Auth::id() == 1)
            <livewire:components.user-chooser name="user" sortBy="last_transaction" />
        @endif

        <div class="field">
            <div class="control">
                <button type="submit" class="button is-link is-fullwidth p-5" wire:loading.attr="disabled">@lang('casino.spin.button')&nbsp;<x-money-format :money="$spinPrice" /></button>
            </div>
        </div>
    </form>
</div>
