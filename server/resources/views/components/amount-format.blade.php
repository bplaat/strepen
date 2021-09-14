<b @if ($amount < 0) class="has-text-danger" @endif>
    {{ App::isLocale('nl') ? number_format($amount, 0, ',', '.') : number_format($amount, 0, '.', ',') }}&times;
</b>
