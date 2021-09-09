<div class="column is-one-quarter">
    <div class="card" style="display: flex; flex-direction: column; height: 100%; margin-bottom: 0; overflow: hidden;">
        <div class="card-content content" style="flex: 1; margin-bottom: 0;">
            <h3 class="is-3">{{ $transaction->name }}</h3>

            @if ($transaction->type == \App\Models\Transaction::TYPE_TRANSACTION)
                <p><i>@lang('admin/transactions.item.transaction_from', ['user.name' => $transaction->user->name, 'transaction.created_at' => $transaction->created_at->format('Y-m-d H:i')])</i></p>
                <p>@lang('admin/transactions.item.amount'): &euro; {{ number_format($transaction->price, 2, ',', '.') }}</p>
                <ul>
                    @foreach ($sortedTransactionProducts as $product)
                        <li><strong>{{ $product->name }}</strong>: {{ number_format($product->pivot->amount, 0, ',', '.') }}</li>
                    @endforeach
                </ul>
            @endif

            @if ($transaction->type == \App\Models\Transaction::TYPE_DEPOSIT)
                <p><i>@lang('admin/transactions.item.deposit_for', ['user.name' => $transaction->user->name, 'transaction.created_at' => $transaction->created_at->format('Y-m-d H:i')])</i></p>
                <p>@lang('admin/transactions.item.amount'): &euro; {{ number_format($transaction->price, 2, ',', '.') }}</p>
            @endif
        </div>

        <div class="card-footer">
            <a href="#" class="card-footer-item" wire:click.prevent="$set('isEditing', true)">@lang('admin/transactions.item.edit')</a>
            <a href="#" class="card-footer-item has-text-danger" wire:click.prevent="$set('isDeleting', true)">@lang('admin/transactions.item.delete')</a>
        </div>
    </div>

    @if ($isEditing)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isEditing', false)"></div>

            <form id="editTransaction" wire:submit.prevent="editTransaction"></form>

            <div class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/transactions.item.edit_transaction')</p>
                    <button type="button" class="delete" wire:click="$set('isEditing', false)"></button>
                </header>

                <section class="modal-card-body">
                    <div class="field">
                        <label class="label" for="user_id">@lang('admin/transactions.item.user')</label>
                        <div class="control">
                            <div class="select is-fullwidth @error('transaction.user_id') is-danger @enderror">
                                <select id="user_id" form="editTransaction" wire:model.defer="transaction.user_id" tabindex="1">
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @error('transaction.user_id') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label class="label" for="name">@lang('admin/transactions.item.name')</label>
                        <div class="control">
                            <input class="input @error('transaction.name') is-danger @enderror" type="text" id="name"
                                form="editTransaction" wire:model.defer="transaction.name" required>
                        </div>
                        @error('transaction.name') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="columns">
                        <div class="column">
                            <div class="field">
                                <label class="label" for="created_at_date">@lang('admin/transactions.item.created_at_date')</label>
                                <div class="control">
                                    <input class="input @error('createdAtDate') is-danger @enderror" type="date" id="created_at_date"
                                        form="editTransaction" wire:model.defer="createdAtDate" tabindex="3" required>
                                </div>
                                @error('createdAtDate') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="column">
                            <div class="field">
                                <label class="label" for="created_at_time">@lang('admin/transactions.item.created_at_time')</label>
                                <div class="control">
                                    <input class="input @error('createdAtTime') is-danger @enderror" type="time" step="1" id="created_at_time"
                                        form="editTransaction" wire:model.defer="createdAtTime" tabindex="4" required>
                                </div>
                                @error('createdAtTime') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    @if ($transaction->type == \App\Models\Transaction::TYPE_TRANSACTION)
                        <div class="field">
                            <label class="label" for="addProductId">@lang('admin/transactions.item.products')</label>
                            <div class="control">
                                <form wire:submit.prevent="addProduct">
                                    <div class="field has-addons">
                                        <div class="control" style="width: 100%;">
                                            <div class="select is-fullwidth">
                                                <select id="addProductId" wire:model.defer="addProductId">
                                                    <option value="null" disabled selected>@lang('admin/transactions.item.select_product')</option>
                                                    @foreach ($products as $product)
                                                        @if (!$transactionProducts->pluck('product_id')->contains($product->id))
                                                            <option value="{{ $product->id }}">{{ $product->name }} (&euro; {{ $product->price }})</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="control">
                                            <button class="button is-link" type="submit">@lang('admin/transactions.item.add_product')</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        @foreach ($transactionProducts as $index => $transactionProduct)
                            <div class="field">
                                <label class="label" for="amount{{ $index }}">
                                    {{ $transactionProduct['product']['name'] }} (&euro; {{ $transactionProduct['product']['price'] }}) @lang('admin/transactions.crud.amount')
                                    <button type="button" class="delete is-pulled-right" wire:click="deleteProduct({{ $transactionProduct['product_id'] }})"></button>
                                </label>
                                <div class="control">
                                    <input class="input @error('transactionProducts.{{ $index }}.amount') is-danger @enderror" type="number" min="1"
                                        id="amount{{ $index }}" form="editTransaction" wire:model="transactionProducts.{{ $index }}.amount" required>
                                </div>
                                @error('transactionProducts.{{ $index }}.amount') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        @endforeach
                    @endif

                    @if ($transaction->type == \App\Models\Transaction::TYPE_DEPOSIT)
                        <div class="field">
                            <label class="label" for="amount">@lang('admin/transactions.item.amount')</label>
                            <p class="control has-icons-left">
                                <input class="input @error('transaction.price') is-danger @enderror" type="number" step="0.01" id="amount"
                                    form="editTransaction" wire:model.defer="transaction.price" required>
                                <span class="icon is-small is-left">&euro;</span>
                            </p>
                            @error('transaction.price') <p class="help is-danger">{{ $message }}</p> @enderror
                        </div>
                    @endif
                </section>

                <footer class="modal-card-foot">
                    <button type="submit" form="editTransaction" class="button is-link">@lang('admin/transactions.item.edit_transaction')</button>
                    <button type="button" class="button" wire:click="$set('isEditing', false)" wire:loading.attr="disabled">@lang('admin/transactions.item.cancel')</button>
                </footer>
            </div>
        </div>
    @endif

    @if ($isDeleting)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isDeleting', false)"></div>

            <div class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/transactions.item.delete_transaction')</p>
                    <button type="button" class="delete" wire:click="$set('isDeleting', false)"></button>
                </header>

                <section class="modal-card-body">
                    <p>@lang('admin/transactions.item.delete_description')</p>
                </section>

                <footer class="modal-card-foot">
                    <button class="button is-danger" wire:click="deleteTransaction()" wire:loading.attr="disabled">@lang('admin/transactions.item.delete_transaction')</button>
                    <button class="button" wire:click="$set('isDeleting', false)" wire:loading.attr="disabled">@lang('admin/transactions.item.cancel')</button>
                </footer>
            </div>
        </div>
    @endif
</div>
