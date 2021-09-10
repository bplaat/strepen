@if (Auth::check() && Auth::id() == 1)
    <div class="navbar is-light is-fixed-top">
        <div class="container">
            <div class="navbar-brand">
                <a class="navbar-item has-text-weight-bold" href="{{ route('kiosk') }}">{{ config('app.name') }} v{{ config('app.version') }}</a>
                <a class="navbar-burger burger"><span></span><span></span><span></span></a>
            </div>
            <div class="navbar-menu">
                <div class="navbar-end">
                    <div class="navbar-item">
                        <div class="buttons">
                            <a class="button is-dark" href="javascript:alert('Nee nee nee, je mag deze prachtige plek niet verlaten!\n1 gratis biertje voor degene die het toch wel lukt.\nHet is niet zo heel erg lastig.')">@lang('layout.navbar.in_kiosk')</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="navbar is-light is-fixed-top">
        <div class="container">
            <div class="navbar-brand">
                <a class="navbar-item has-text-weight-bold" href="{{ route('home') }}">{{ config('app.name') }} v{{ config('app.version') }}</a>
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
                                    <a class="navbar-item"
                                        href="{{ route('admin.users.index') }}">@lang('layout.navbar.admin_users')</a>
                                    <a class="navbar-item"
                                        href="{{ route('admin.posts.index') }}">@lang('layout.navbar.admin_posts')</a>
                                    <a class="navbar-item"
                                        href="{{ route('admin.products.index') }}">@lang('layout.navbar.admin_products')</a>
                                    <a class="navbar-item"
                                        href="{{ route('admin.inventories.index') }}">@lang('layout.navbar.admin_inventories')</a>
                                    <a class="navbar-item"
                                        href="{{ route('admin.transactions.index') }}">@lang('layout.navbar.admin_transactions')</a>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="navbar-end">
                        <div class="navbar-item" style="display: flex; align-items: center;">
                            <div style="width: 28px; height: 28px; border-radius: 50%; margin-right: 10px; background-size: cover; background-position: center center;
                                background-image: url({{ Auth::user()->avatar != null ? '/storage/avatars/' . Auth::user()->avatar : '/images/avatars/mp.jpg' }});"></div>
                            <span style="margin-right: 8px;">{{ Auth::user()->name }}</span>
                            <strong @if (Auth::user()->balance < 0) class="has-text-danger" @endif>&euro; {{ number_format(Auth::user()->balance, 2, ',', '.') }}</strong>
                        </div>
                        <div class="navbar-item">
                            <div class="buttons">
                                @if (Auth::user()->role == App\Models\User::ROLE_ADMIN)
                                    <a class="button is-dark" href="{{ route('admin.kiosk') }}">@lang('layout.navbar.admin_kiosk')</a>
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
