<span @class(['has-text-danger' => $amount < 0]) @if ($isBold) style="font-weight: 600;" @endif>
    {{App::isLocale('nl')?number_format($amount,0,',','.') : number_format($amount,0,'.',',')}}@if($isPerHour)/h @else&times;@endif
</span>
