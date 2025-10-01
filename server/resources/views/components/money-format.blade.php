<span @class(['has-text-danger' => $money < 0]) @if ($isBold) style="font-weight: 600;" @endif>
    {{ App\Models\Setting::get('currency_symbol') }} {{ App::isLocale('nl') ? number_format($money, 2, ',', '.') : number_format($money, 2, '.', ',') }}@if ($isPerHour)/h @endif
</span>
