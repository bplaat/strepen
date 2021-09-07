@component('layouts.app')
    @slot('title', __('settings.title'))

    <h1 class="title">@lang('settings.title')</h1>

    @livewire('settings.change-details')

    @livewire('settings.change-avatar')

    @livewire('settings.change-password')
@endcomponent
