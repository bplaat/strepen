<span @if ($amount < 0) class="has-text-danger" @endif @if ($isBold) style="font-weight: 600;" @endif>
    {{ App::isLocale('nl') ? number_format($amount, 0, ',', '.') : number_format($amount, 0, '.', ',') }}&times;
</span>
