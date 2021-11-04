@if ($index == 0)
    <div style="width: 24px; line-height: 24px; text-align: center; border-radius: 50%; font-weight: bold; background-color: #ffd700; color: #111;">1</div>
@elseif ($index == 1)
    <div style="width: 24px; line-height: 24px; text-align: center; border-radius: 50%; font-weight: bold; background-color: #c0c0c0; color: #111;">2</div>
@elseif ($index == 2)
    <div style="width: 24px; line-height: 24px; text-align: center; border-radius: 50%; font-weight: bold; background-color: #cd7f32; color: #111;">3</div>
@else
    <div style="width: 24px; line-height: 24px; text-align: center;">{{ $index + 1 }}</div>
@endif
