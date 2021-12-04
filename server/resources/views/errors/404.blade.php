@component('layouts.app')
    @slot('title', __('errors.404.title'))
    <div class="container content">
        <h1 class="title">@lang('errors.404.title')</h1>
        <p>@lang('errors.404.information', ['url' => Request::fullUrl()])</p>
        <p>@lang('errors.404.help')</p>
    </div>
@endcomponent
