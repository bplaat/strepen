<div class="container">
    @component('components.search-header', ['itemName' => __('transactions.history.transactions')])
        <h1 class="title is-4">@lang('transactions.history.header')</h1>
    @endcomponent

    @if ($transactions->count() > 0)
        {{ $transactions->links() }}

        <div class="columns is-multiline">
            @foreach ($transactions as $transaction)
                <div class="column is-one-quarter">
                    <div class="card" style="height: 100%;">
                        <div class="card-content content">
                            <h4>{{ $transaction->name }}</h4>

                            @if ($transaction->type == \App\Models\Transaction::TYPE_TRANSACTION)
                                <p><i>@lang('transactions.history.transaction_from', ['transaction.created_at' => $transaction->created_at->format('Y-m-d H:i')])</i></p>
                                <p>@lang('transactions.history.cost'): &euro; {{ number_format($transaction->price, 2, ',', '.') }}</p>
                                <ul>
                                    @foreach ($transaction->products->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE) as $product)
                                        <li><strong>{{ $product->name }}</strong>: {{ number_format($product->pivot->amount, 0, ',', '.') }}</li>
                                    @endforeach
                                </ul>
                            @endif

                            @if ($transaction->type == \App\Models\Transaction::TYPE_DEPOSIT)
                                <p><i>@lang('transactions.history.deposit_for', ['transaction.created_at' => $transaction->created_at->format('Y-m-d H:i')])</i></p>
                                <p>@lang('transactions.history.amount'): &euro; {{ number_format($transaction->price, 2, ',', '.') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{ $transactions->links() }}
    @else
        <p><i>@lang('transactions.history.empty')</i></p>
    @endif
</div>
