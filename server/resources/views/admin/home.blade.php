@component('layouts.app')
    @slot('title', __('admin/home.title'))
    <div class="container">
        <h1 class="title">@lang('admin/home.header')</h1>

        <div class="buttons">
            <a class="button" href="{{ route('admin.api_keys.crud') }}">@lang('admin/home.api_keys')</a>
            <a class="button" href="{{ route('admin.users.crud') }}">@lang('admin/home.users')</a>
            <a class="button" href="{{ route('admin.posts.crud') }}">@lang('admin/home.posts')</a>
            <a class="button" href="{{ route('admin.products.crud') }}">@lang('admin/home.products')</a>
            <a class="button" href="{{ route('admin.inventories.crud') }}">@lang('admin/home.inventories')</a>
            <a class="button" href="{{route('admin.transactions.crud')}}">@lang('admin/home.transactions')</a>
        </div>
    </div>
@endcomponent
