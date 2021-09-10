<div>
    <div class="columns">
        <div class="column">
            <h1 class="title is-4">@lang('transactions.history.header')</h1>
        </div>
        <div class="column">
            <form wire:submit.prevent="$refresh">
                <div class="field has-addons">
                    <div class="control" style="width: 100%;">
                        <input class="input" type="text" id="q" wire:model.defer="q" placeholder="@lang('transactions.history.query')">
                    </div>
                    <div class="control">
                        <button class="button is-link" type="submit">@lang('transactions.history.search')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

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
                                <p>@lang('transactions.history.amount'): &euro; {{ number_format($transaction->price, 2, ',', '.') }}</p>
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
