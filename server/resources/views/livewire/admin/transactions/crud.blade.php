<div class="container">
    <h2 class="title is-4">@lang('admin/transactions.crud.header')</h2>

    <div class="columns">
        <div class="column">
            <div class="buttons">
                <button class="button is-link" wire:click="openCreateTransaction" wire:loading.attr="disabled">@lang('admin/transactions.crud.create_transaction')</button>
                <button class="button is-link" wire:click="openCreateDeposit" wire:loading.attr="disabled">@lang('admin/transactions.crud.create_deposit')</button>
            </div>
        </div>

        <div class="column">
            <form wire:submit.prevent="$refresh">
                <div class="field has-addons">
                    <div class="control" style="width: 100%;">
                        <input class="input" type="text" id="q" wire:model.defer="q" placeholder="@lang('admin/transactions.crud.query')">
                    </div>
                    <div class="control">
                        <button class="button is-link" type="submit">@lang('admin/transactions.crud.search')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if ($transactions->count() > 0)
        {{ $transactions->links() }}

        <div class="columns is-multiline">
            @foreach ($transactions as $transaction)
                @livewire('admin.transactions.item', ['transaction' => $transaction], key($transaction->id))
            @endforeach
        </div>

        {{ $transactions->links() }}
    @else
        <p><i>@lang('admin/transactions.crud.empty')</i></p>
    @endif

    @if ($isCreatingTransaction)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isCreatingTransaction', false)"></div>

            <form id="createTransaction" wire:submit.prevent="createTransaction"></form>

            <div class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/transactions.crud.create_transaction')</p>
                    <button type="button" class="delete" wire:click="$set('isCreatingTransaction', false)"></button>
                </div>

                <div class="modal-card-body">
                    <div class="field">
                        <label class="label" for="user_id">@lang('admin/transactions.crud.user')</label>
                        <div class="control">
                            <div class="select is-fullwidth @error('transaction.user_id') is-danger @enderror">
                                <select id="user_id" form="createTransaction" wire:model.defer="transaction.user_id">
                                    <option value="null" disabled selected>@lang('admin/transactions.crud.select_user')</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @error('transaction.user_id') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label class="label" for="name">@lang('admin/transactions.crud.name')</label>
                        <div class="control">
                            <input class="input @error('transaction.name') is-danger @enderror" type="text" id="name"
                                form="createTransaction" wire:model.defer="transaction.name" required>
                        </div>
                        @error('transaction.name') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label class="label" for="addProductId">@lang('admin/transactions.crud.products')</label>
                        <div class="control">
                            <form wire:submit.prevent="addProduct">
                                <div class="field has-addons">
                                    <div class="control" style="width: 100%;">
                                        <div class="select is-fullwidth">
                                            <select id="addProductId" wire:model.defer="addProductId">
                                                <option value="null" disabled selected>@lang('admin/transactions.crud.select_product')</option>
                                                @foreach ($products as $product)
                                                    @if (!$transactionProducts->pluck('product_id')->contains($product->id))
                                                        <option value="{{ $product->id }}">{{ $product->name }} (&euro; {{ $product->price }})</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="control">
                                        <button class="button is-link" type="submit">@lang('admin/transactions.crud.add_product')</button>
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
                                        {{ $transactionProduct['product']['name'] }} (&euro; {{ $transactionProduct['product']['price'] }}) @lang('admin/transactions.crud.amount'):
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
                </div>

                <div class="modal-card-foot">
                    <button type="submit" form="createTransaction" class="button is-link">@lang('admin/transactions.crud.create_transaction')</button>
                    <button type="button" class="button" wire:click="$set('isCreatingTransaction', false)" wire:loading.attr="disabled">@lang('admin/transactions.crud.cancel')</button>
                </div>
            </div>
        </div>
    @endif

    @if ($isCreatingDeposit)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isCreatingDeposit', false)"></div>

            <form wire:submit.prevent="createDeposit" class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/transactions.crud.create_deposit')</p>
                    <button type="button" class="delete" wire:click="$set('isCreatingDeposit', false)"></button>
                </div>

                <div class="modal-card-body">
                    <div class="field">
                        <label class="label" for="user_id">@lang('admin/transactions.crud.user')</label>
                        <div class="control">
                            <div class="select is-fullwidth @error('transaction.user_id') is-danger @enderror">
                                <select id="user_id" wire:model.defer="transaction.user_id">
                                    <option value="null" disabled selected>@lang('admin/transactions.crud.select_user')</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @error('transaction.user_id') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label class="label" for="name">@lang('admin/transactions.crud.name')</label>
                        <div class="control">
                            <input class="input @error('transaction.name') is-danger @enderror" type="text" id="name"
                                wire:model.defer="transaction.name" required>
                        </div>
                        @error('transaction.name') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label class="label" for="amount">@lang('admin/transactions.crud.amount')</label>
                        <p class="control has-icons-left">
                            <input class="input @error('transaction.price') is-danger @enderror" type="number" step="0.01" id="amount"
                                wire:model.defer="transaction.price" required>
                            <span class="icon is-small is-left">&euro;</span>
                        </p>
                        @error('transaction.price') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="modal-card-foot">
                    <button type="submit" class="button is-link">@lang('admin/transactions.crud.create_deposit')</button>
                    <button type="button" class="button" wire:click="$set('isCreatingDeposit', false)" wire:loading.attr="disabled">@lang('admin/transactions.crud.cancel')</button>
                </div>
            </form>
        </div>
    @endif
</div>
