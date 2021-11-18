<span @if ($money < 0) class="has-text-danger" @endif @if ($isBold) style="font-weight: 600;" @endif>
    &euro; {{ App::isLocale('nl') ? number_format($money, 2, ',', '.') : number_format($money, 2, '.', ',') }}
</span>
