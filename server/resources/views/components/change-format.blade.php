<span @class(['has-text-success' => $change > 0, 'has-text-grey' => $change == 0, 'has-text-danger' => $change < 0])
    @if ($isBold) style="font-weight: 600;" @endif>
    @if ($change > 0) &#9650; @endif @if ($change == 0) &#9644; @endif  @if ($change < 0) &#9660; @endif
    @if ($isMoney)
        &euro; {{ App::isLocale('nl') ? number_format($change, 2, ',', '.') : number_format($change, 2, '.', ',') }}
    @else
        {{ App::isLocale('nl') ? number_format($change, 0, ',', '.') : number_format($change, 0, '.', ',') }}
    @endif
</span>
