@php
    $isDark = Auth::check() && Auth::user()->theme == \App\Models\User::THEME_DARK;
@endphp

@if (Auth::check() && Auth::id() == 1)
    <div class="navbar @if ($isDark) is-dark @else is-light @endif is-fixed-top">
        <div class="container">
            <div class="navbar-brand">
                <a class="navbar-item has-text-weight-bold" href="{{ route('kiosk') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width: 24px; height: 24px; margin-right: 10px;" viewBox="0 0 24 24">
                        <path fill="@if ($isDark) #fff @else #111 @endif"
                            d="M9.5 3C7.56 3 5.85 4.24 5.23 6.08C3.36 6.44 2 8.09 2 10C2 12.21 3.79 14 6 14V22H17V20H20C20.55 20 21 19.55 21 19V11C21 10.45 20.55 10 20 10H18V8C18 5.79 16.21 4 14 4H12.32C11.5 3.35 10.53 3 9.5 3M9.5 5C10.29 5 11.03 5.37 11.5 6H14C15.11 6 16 6.9 16 8H12C10 8 9.32 9.13 8.5 10.63C7.68 12.13 6 12 6 12C4.89 12 4 11.11 4 10C4 8.9 4.89 8 6 8H7V7.5C7 6.12 8.12 5 9.5 5M17 12H19V18H17Z" />
                    </svg>
                    {{ config('app.name') }} v{{ config('app.version') }}
                </a>
                <a class="navbar-burger burger"><span></span><span></span><span></span></a>
            </div>
            <div class="navbar-menu">
                <div class="navbar-end">
                    <div class="navbar-item">
                        <div class="buttons">
                            <a class="button @if (!$isDark) is-dark @endif" href="javascript:alert('Nee nee nee, je mag deze prachtige plek niet verlaten!\n1 gratis biertje voor degene die het toch wel lukt.\nHet is niet zo heel erg lastig.')">@lang('layout.navbar.in_kiosk')</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="navbar @if ($isDark) is-dark @else is-light @endif is-fixed-top">
        <div class="container">
            <div class="navbar-brand">
                <a class="navbar-item has-text-weight-bold" href="{{ route('home') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width: 24px; height: 24px; margin-right: 10px;" viewBox="0 0 24 24">
                        <path fill="@if ($isDark) #fff @else #111 @endif"
                            d="M9.5 3C7.56 3 5.85 4.24 5.23 6.08C3.36 6.44 2 8.09 2 10C2 12.21 3.79 14 6 14V22H17V20H20C20.55 20 21 19.55 21 19V11C21 10.45 20.55 10 20 10H18V8C18 5.79 16.21 4 14 4H12.32C11.5 3.35 10.53 3 9.5 3M9.5 5C10.29 5 11.03 5.37 11.5 6H14C15.11 6 16 6.9 16 8H12C10 8 9.32 9.13 8.5 10.63C7.68 12.13 6 12 6 12C4.89 12 4 11.11 4 10C4 8.9 4.89 8 6 8H7V7.5C7 6.12 8.12 5 9.5 5M17 12H19V18H17Z" />
                    </svg>
                    {{ config('app.name') }} v{{ config('app.version') }}
                </a>
                <a class="navbar-burger burger"><span></span><span></span><span></span></a>
            </div>
            <div class="navbar-menu">
                @auth
                    <div class="navbar-start">
                        <a class="navbar-item" href="{{ route('transactions.create') }}">@lang('layout.navbar.transactions_create')</a>
                        <a class="navbar-item" href="{{ route('transactions.history') }}">@lang('layout.navbar.transactions_history')</a>

                        @if (Auth::user()->role == App\Models\User::ROLE_ADMIN)
                            <div class="navbar-item has-dropdown is-hoverable">
                                <a class="navbar-link is-arrowless"
                                    href="{{ route('admin.home') }}">@lang('layout.navbar.admin_home')</a>
                                <div class="navbar-dropdown">
                                    <a class="navbar-item" href="{{ route('admin.users.crud') }}">@lang('layout.navbar.admin_users')</a>
                                    <a class="navbar-item" href="{{ route('admin.posts.crud') }}">@lang('layout.navbar.admin_posts')</a>
                                    <a class="navbar-item" href="{{ route('admin.products.crud') }}">@lang('layout.navbar.admin_products')</a>
                                    <a class="navbar-item" href="{{ route('admin.inventories.crud') }}">@lang('layout.navbar.admin_inventories')</a>
                                    <a class="navbar-item" href="{{ route('admin.transactions.crud') }}">@lang('layout.navbar.admin_transactions')</a>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="navbar-end">
                        <a class="navbar-item" href="{{ route('balance') }}" style="display: flex; align-items: center;">
                            <div style="width: 28px; height: 28px; border-radius: 50%; margin-right: 10px; background-size: cover; background-position: center center;
                                background-image: url({{ Auth::user()->avatar != null ? '/storage/avatars/' . Auth::user()->avatar : '/images/avatars/mp.jpg' }});"></div>
                            <span style="margin-right: 8px;">{{ Auth::user()->name }}</span>
                            @component('components.money-format', ['money' => Auth::user()->balance])@endcomponent
                        </a>
                        <div class="navbar-item">
                            <div class="buttons">
                                @if (Auth::user()->role == App\Models\User::ROLE_ADMIN)
                                    <a class="button @if (!$isDark) is-dark @endif" href="{{ route('admin.kiosk') }}">@lang('layout.navbar.admin_kiosk')</a>
                                @endif
                                <a class="button is-link" href="{{ route('settings') }}">@lang('layout.navbar.settings')</a>
                                <a class="button" href="{{ route('auth.logout') }}">@lang('layout.navbar.logout')</a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="navbar-end">
                        <div class="navbar-item">
                            <div class="buttons">
                                <a class="button is-link" href="{{ route('auth.login') }}">@lang('layout.navbar.login')</a>
                            </div>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </div>
@endif
