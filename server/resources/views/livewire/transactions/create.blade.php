<div>
    @if (session()->has('create_transaction_message'))
        <div class="notification is-success">
            <button class="delete" onclick="this.parentNode.parentNode.removeChild(this.parentNode);"></button>
            <p>{{ session('create_transaction_message') }}</p>
        </div>
    @endif

    <h1 class="title is-4">@lang('transactions.create.header')</h1>

    <form id="createTransaction" wire:submit.prevent="createTransaction"></form>

    <div class="field">
        <label class="label" for="name">@lang('transactions.create.name')</label>
        <div class="control">
            <input class="input @error('transaction.name') is-danger @enderror" type="text" id="name"
                form="createTransaction" wire:model.defer="transaction.name" required>
        </div>
        @error('transaction.name') <p class="help is-danger">{{ $message }}</p> @enderror
    </div>

    <div class="field">
        <label class="label" for="addProductId">@lang('transactions.create.products')</label>
        <div class="control">
            <form wire:submit.prevent="addProduct">
                <div class="field has-addons">
                    <div class="control" style="width: 100%;">
                        <div class="select is-fullwidth">
                            <select id="addProductId" wire:model.defer="addProductId">
                                <option value="null" disabled selected>@lang('transactions.create.select_product')</option>
                                @foreach ($products as $product)
                                    @if (!$transactionProducts->pluck('product_id')->contains($product->id))
                                        <option value="{{ $product->id }}">{{ $product->name }} (&euro; {{ $product->price }})</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="control">
                        <button class="button is-link" type="submit">@lang('transactions.create.add_product')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="field">
        @foreach ($transactionProducts as $index => $transactionProduct)
            <div class="media" style="display: flex; align-items: center;">
                <div class="media-left">
                    <div style="width: 64px; height: 64px; background-size: cover; background-position: center center;
                        background-image: url({{ $transactionProduct['product']['image'] != null ? '/storage/products/' . $transactionProduct['product']['image'] : '/images/products/unkown.png' }});"></div>
                </div>
                <div class="media-content">
                    <label class="label" for="amount{{ $index }}">
                        {{ $transactionProduct['product']['name'] }} (&euro; {{ $transactionProduct['product']['price'] }}) @lang('transactions.create.amount'):
                        <button type="button" class="delete is-pulled-right" wire:click="deleteProduct({{ $transactionProduct['product_id'] }})"></button>
                    </label>
                    <div class="control">
                        <input class="input @error('transactionProducts.{{ $index }}.amount') is-danger @enderror" type="number"
                            min="1" id="amount{{ $index }}" form="createTransaction"
                            wire:model="transactionProducts.{{ $index }}.amount" required>
                    </div>
                    @error('transactionProducts.{{ $index }}.amount') <p class="help is-danger">{{ $message }}</p> @enderror
                </div>
            </div>
        @endforeach
    </div>

    <div class="field">
        <div class="control">
            <button type="submit" form="createTransaction" class="button is-link">@lang('transactions.create.create_transaction')</button>
        </div>
    </div>
</div>
