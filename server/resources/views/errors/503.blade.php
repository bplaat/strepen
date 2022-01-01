@component('layouts.app')
    @slot('title', __('errors.503.title'))
    <div class="container content">
        <h1 class="title">@lang('errors.503.title')</h1>
        <p>@lang('errors.503.information', ['url' => Request::fullUrl()])</p>
        <p>@lang('errors.503.help')</p>
    </div>
@endcomponent
