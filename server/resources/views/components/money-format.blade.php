<span @class(['has-text-danger' => $money < 0]) @if ($isBold) style="font-weight: 600;" @endif>
    &euro; {{ App::isLocale('nl') ? number_format($money, 2, ',', '.') : number_format($money, 2, '.', ',') }}
</span>
