<span
    class="@if ($change > 0) has-text-success @endif @if ($change == 0) has-text-grey @endif @if ($change < 0) has-text-danger @endif"
    @if ($isBold) style="font-weight: 600;" @endif
>
    @if ($change > 0) &#9650; @endif @if ($change == 0) &#9644; @endif  @if ($change < 0) &#9660; @endif
    @if ($isMoney)
        &euro; {{ App::isLocale('nl') ? number_format($change, 2, ',', '.') : number_format($change, 2, '.', ',') }}
    @else
        {{ App::isLocale('nl') ? number_format($change, 0, ',', '.') : number_format($change, 0, '.', ',') }}
    @endif
</span>
