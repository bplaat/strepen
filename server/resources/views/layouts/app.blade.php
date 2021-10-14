<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="has-navbar-fixed-top">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $title }} - {{ config('app.name') }}</title>
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="16x16" href="/images/icon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/images/icon-32x32.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/images/apple-touch-icon.png">
    <link rel="mask-icon" href="/images/safari-pinned-tab.svg" color="#111">
    <meta name="theme-color" content="#f5f5f5">
    <link rel="manifest" href="/manifest.json">
    @if (Auth::check() && Auth::user()->theme == \App\Models\User::THEME_LIGHT)
        <link rel="stylesheet" href="/css/bulma-light.min.css">
    @else
        <link rel="stylesheet" href="/css/bulma-dark.min.css">
    @endif
    @livewireStyles
    @if (isset($chartjs))
        <script src="/js/chart.min.js"></script>
    @endif
</head>
<body>
    @include('layouts.navbar')

    <div class="section">
        {{ $slot }}
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
