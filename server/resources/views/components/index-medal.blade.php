@if ($index == 0)
    <div class="medal is-gold">1</div>
@elseif ($index == 1)
    <div class="medal is-silver">2</div>
@elseif ($index == 2)
    <div class="medal is-bronze">3</div>
@else
    <div class="medal">{{ $index + 1 }}</div>
@endif
