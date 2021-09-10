@component('layouts.app')
    @slot('title', __('errors.404.title'))

    <div class="content">
        <h1 class="title">@lang('errors.404.title')</h1>
        <p>@lang('errors.404.information', ['url' => $_SERVER['REQUEST_URI']])</p>
        <p>@lang('errors.404.help')</p>
    </div>
@endcomponent