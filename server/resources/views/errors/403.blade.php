@component('layouts.app')
    @slot('title', __('errors.403.title'))
    <div class="container content">
        <h1 class="title">@lang('errors.403.title')</h1>
        <p>@lang('errors.403.information', ['url' => Request::fullUrl()])</p>
        <p>@lang('errors.403.help')</p>
    </div>
@endcomponent
