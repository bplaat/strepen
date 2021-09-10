<div>
    <h1 class="title">@lang('balance.header')</h1>
    <canvas id="balance_chart_canvas"></canvas>

    <script>
    new Chart(document.getElementById('balance_chart_canvas').getContext('2d'), {
        type: 'line',
        data: {
            datasets: [{
                label: 'Balance (\u20ac)',
                data: @json($balanceChart),
                borderColor: '#3e56c4',
                tension: 0.1
            }]
        },
        options: {
            animation: false
        }
    });
    </script>
</div>
