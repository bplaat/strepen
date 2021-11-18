<div class="container">
    <x-search-header :itemName="__('transactions.history.transactions')">
        <h1 class="title is-4">@lang('transactions.history.header')</h1>

        <x-slot name="fields">
            <x-transaction-type-chooser />

            <livewire:components.product-chooser :productId="$product_id" inline="true" relationship="true" />
        </x-slot>
    </x-search-header>

    @if ($transactions->count() > 0)
        {{ $transactions->links() }}

        <div class="columns is-multiline">
            @foreach ($transactions as $transaction)
                <div class="column is-one-quarter">
                    <div class="card">
                        <div class="card-content content">
                            <h4>{{ $transaction->name }}</h4>

                            @if ($transaction->type == App\Models\Transaction::TYPE_TRANSACTION)
                                <p><i>@lang('transactions.history.transaction_on', ['transaction.created_at' => $transaction->created_at->format('Y-m-d H:i')])</i></p>
                                <p>@lang('transactions.history.cost'): <x-money-format :money="$transaction->price" /></p>

                                @foreach ($transaction->products()->orderByRaw('LOWER(name)')->get() as $product)
                                    <p>
                                        <div class="image is-small is-rounded is-inline" style="background-image: url(/storage/products/{{ $product->image ?? App\Models\Setting::get('default_product_image') }});"></div>
                                        <b>{{ $product->name }}</b>: <x-amount-format :amount="$product->pivot->amount" />
                                    </p>
                                @endforeach
                            @endif

                            @if ($transaction->type == App\Models\Transaction::TYPE_DEPOSIT)
                                <p><i>@lang('transactions.history.deposit_on', ['transaction.created_at' => $transaction->created_at->format('Y-m-d H:i')])</i></p>
                                <p>@lang('transactions.history.amount'): <x-money-format :money="$transaction->price" /></p>
                            @endif

                            @if ($transaction->type == App\Models\Transaction::TYPE_FOOD)
                                <p><i>@lang('transactions.history.food_on', ['transaction.created_at' => $transaction->created_at->format('Y-m-d H:i')])</i></p>
                                <p>@lang('transactions.history.amount'): <x-money-format :money="$transaction->price" /></p>
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
