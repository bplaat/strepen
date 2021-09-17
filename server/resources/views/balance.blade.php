@component('layouts.app')
    @slot('title', __('balance.title'))
    @slot('chartjs', true)

    <div class="container">
        <h1 class="title">@lang('balance.header')</h1>
        <canvas id="balance_chart_canvas"></canvas>

        <script>
        new Chart(document.getElementById('balance_chart_canvas').getContext('2d'), {
            type: 'line',
            data: {
                datasets: [{
                    label: 'Balance (\u20ac)',
                    data: @json(Auth::user()->getBalanceChart()),
                    borderColor: getComputedStyle(document.querySelector('.is-link')).backgroundColor,
                    tension: 0.1
                }]
            },
            options: {
                animation: false
            }
        });
        </script>
    </div>
@endcomponent
