<b @if ($money < 0) class="has-text-danger" @endif>
    &euro; {{ App::isLocale('nl') ? number_format($money, 2, ',', '.') : number_format($money, 2, '.', ',') }}
</b>
