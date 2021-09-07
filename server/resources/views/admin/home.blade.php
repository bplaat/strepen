@component('layouts.app')
    @slot('title', __('admin/home.title'))

    <h1 class="title">@lang('admin/home.header')</h1>

    <div class="buttons">
        <a class="button" href="{{ route('admin.users.index') }}">@lang('admin/home.users')</a>
        <a class="button" href="{{ route('admin.posts.index') }}">@lang('admin/home.posts')</a>
        <a class="button" href="{{ route('admin.products.index') }}">@lang('admin/home.products')</a>
        <a class="button" href="{{ route('admin.inventories.index') }}">@lang('admin/home.inventories')</a>
        <a class="button" href="{{route('admin.transactions.index')}}">@lang('admin/home.transactions')</a>
    </div>
@endcomponent
