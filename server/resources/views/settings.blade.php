@component('layouts.app')
    @slot('title', __('settings.title'))
    <div class="container">
        <h1 class="title">@lang('settings.title')</h1>

        @livewire('settings.change-details')

        <div class="columns">
            <div class="column">
                @livewire('settings.change-avatar')
            </div>

            <div class="column">
                @livewire('settings.change-thanks')
            </div>
        </div>

        @livewire('settings.change-password')
    </div>
@endcomponent
