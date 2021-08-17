<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="has-navbar-fixed-top">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title') - {{ config('app.name') }}</title>
    <link rel="stylesheet" href="/css/bulma.min.css">
    @livewireStyles
</head>
<body>
@hasSection('navbar')
        @yield('navbar')
    @else
        <div class="navbar is-light is-fixed-top">
            <div class="container">
                <div class="navbar-brand">
                    <a class="navbar-item has-text-weight-bold" href="{{ route('home') }}">
                        {{ config('app.name') }}
                    </a>
                    <a class="navbar-burger burger"><span></span><span></span><span></span></a>
                </div>
                <div class="navbar-menu">
                    @auth
                        <div class="navbar-start">
                            <a class="navbar-item" href="#">TODO</a>
                            <a class="navbar-item" href="#">TODO</a>
                            <a class="navbar-item" href="#">TODO</a>

                            @if (Auth::user()->role == App\Models\User::ROLE_ADMIN)
                                <div class="navbar-item has-dropdown is-hoverable">
                                    <a class="navbar-link is-arrowless" href="{{ route('admin.home') }}">@lang('layout.header.admin_home')</a>
                                    <div class="navbar-dropdown">
                                        <a class="navbar-item" href="{{ route('admin.users.index') }}">@lang('layout.header.admin_users')</a>
                                        <a class="navbar-item" href="{{ route('admin.posts.index') }}">@lang('layout.header.admin_posts')</a>
                                        <a class="navbar-item" href="{{ route('admin.products.index') }}">@lang('layout.header.admin_products')</a>
                                        <a class="navbar-item" href="{{ route('admin.inventories.index') }}">@lang('layout.header.admin_inventories')</a>
                                        <a class="navbar-item" href="{{ route('admin.transactions.index') }}">@lang('layout.header.admin_transactions')</a>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="navbar-end">
                            <div class="navbar-item" style="display: flex; align-items: center;">
                                <img style="width: 28px; height: 28px; border-radius: 50%; margin-right: 10px;" src="{{ Auth::user()->avatar != null ? '/storage/avatars/' . Auth::user()->avatar : '/images/avatars/mp.jpg' }}" alt="@lang('layout.header.avatar_alt', [ 'user.name' => Auth::user()->name ])">
                                <span style="margin-right: 8px;">{{ Auth::user()->name }}</span>
                                <strong @if (Auth::user()->money < 0) class="has-text-danger" @endif>&euro; {{ number_format(Auth::user()->money, 2, ',', '.') }}</strong>
                            </div>
                            <div class="navbar-item">
                                <div class="buttons">
                                    <a class="button is-link" href="{{ route('settings') }}">@lang('layout.header.settings')</a>
                                    <a class="button" href="{{ route('auth.logout') }}">@lang('layout.header.logout')</a>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="navbar-end">
                            <div class="navbar-item">
                                <div class="buttons">
                                    <a class="button is-link" href="{{ route('auth.login') }}">@lang('layout.header.login')</a>
                                </div>
                            </div>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    @endif

    <div class="section">
        <div class="container">
            @yield('content')
        </div>
    </div>

    <div class="footer">
        <div class="content has-text-centered">
            <p>@lang('layout.footer.authors')</p>
            <p>@lang('layout.footer.source')</p>
        </div>
    </div>

    <script src="/js/script.js"></script>
    @livewireScripts
</body>
</html>
