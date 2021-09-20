<div class="column is-one-quarter">
    <div class="card" style="display: flex; flex-direction: column; height: 100%; margin-bottom: 0; overflow: hidden;">
        <div class="card-content content" style="flex: 1; margin-bottom: 0;">
            <h4>{{ $transaction->name }}</h4>

            @if ($transaction->type == \App\Models\Transaction::TYPE_TRANSACTION)
                <p><i>@lang('admin/transactions.item.transaction_from', ['user.name' => $transaction->user->name, 'transaction.created_at' => $transaction->created_at->format('Y-m-d H:i')])</i></p>
                <p>@lang('admin/transactions.item.cost'): @component('components.money-format', ['money' => $transaction->price])@endcomponent</p>
                <ul>
                    @foreach ($transaction->products->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE) as $product)
                        <li><b>{{ $product->name }}</b>: @component('components.amount-format', ['amount' => $product->pivot->amount])@endcomponent</li>
                    @endforeach
                </ul>
            @endif

            @if ($transaction->type == \App\Models\Transaction::TYPE_DEPOSIT)
                <p><i>@lang('admin/transactions.item.deposit_for', ['user.name' => $transaction->user->name, 'transaction.created_at' => $transaction->created_at->format('Y-m-d H:i')])</i></p>
                <p>@lang('admin/transactions.item.amount'): @component('components.money-format', ['money' => $transaction->price])@endcomponent</p>
            @endif

            @if ($transaction->type == \App\Models\Transaction::TYPE_FOOD)
                <p><i>@lang('admin/transactions.item.food_for', ['user.name' => $transaction->user->name, 'transaction.created_at' => $transaction->created_at->format('Y-m-d H:i')])</i></p>
                <p>@lang('admin/transactions.item.amount'): @component('components.money-format', ['money' => $transaction->price])@endcomponent</p>
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

            <form id="mainForm" wire:submit.prevent="editTransaction"></form>

            <div class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/transactions.item.edit_transaction')</p>
                    <button type="button" class="delete" wire:click="$set('isEditing', false)"></button>
                </div>

                <div class="modal-card-body">
                    <div class="field">
                        <label class="label" for="user_id">@lang('admin/transactions.item.user')</label>
                        <div class="control">
                            <div class="select is-fullwidth @error('transaction.user_id') is-danger @enderror">
                                <select id="user_id" form="mainForm" wire:model.defer="transaction.user_id" tabindex="1">
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
                                form="mainForm" wire:model.defer="transaction.name" required>
                        </div>
                        @error('transaction.name') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="columns">
                        <div class="column">
                            <div class="field">
                                <label class="label" for="created_at_date">@lang('admin/transactions.item.created_at_date')</label>
                                <div class="control">
                                    <input class="input @error('createdAtDate') is-danger @enderror" type="date" id="created_at_date"
                                        form="mainForm" wire:model.defer="createdAtDate" tabindex="3" required>
                                </div>
                                @error('createdAtDate') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="column">
                            <div class="field">
                                <label class="label" for="created_at_time">@lang('admin/transactions.item.created_at_time')</label>
                                <div class="control">
                                    <input class="input @error('createdAtTime') is-danger @enderror" type="time" step="1" id="created_at_time"
                                        form="mainForm" wire:model.defer="createdAtTime" tabindex="4" required>
                                </div>
                                @error('createdAtTime') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    @if ($transaction->type == \App\Models\Transaction::TYPE_TRANSACTION)
                        @livewire('components.products-chooser', ['selectedProducts' => $selectedProducts, 'nomax' => true])
                    @endif

                    @if ($transaction->type == \App\Models\Transaction::TYPE_DEPOSIT || $transaction->type == \App\Models\Transaction::TYPE_FOOD)
                        <div class="field">
                            <label class="label" for="amount">@lang('admin/transactions.item.amount')</label>
                            <p class="control has-icons-left">
                                <input class="input @error('transaction.price') is-danger @enderror" type="number" step="0.01" id="amount"
                                    form="mainForm" wire:model.defer="transaction.price" required>
                                <span class="icon is-small is-left">&euro;</span>
                            </p>
                            @error('transaction.price') <p class="help is-danger">{{ $message }}</p> @enderror
                        </div>
                    @endif
                </div>

                <div class="modal-card-foot">
                    <button type="submit" form="mainForm" class="button is-link">@lang('admin/transactions.item.edit_transaction')</button>
                    <button type="button" class="button" wire:click="$set('isEditing', false)" wire:loading.attr="disabled">@lang('admin/transactions.item.cancel')</button>
                </div>
            </div>
        </div>
    @endif

    @if ($isDeleting)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isDeleting', false)"></div>

            <div class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/transactions.item.delete_transaction')</p>
                    <button type="button" class="delete" wire:click="$set('isDeleting', false)"></button>
                </div>

                <div class="modal-card-body">
                    <p>@lang('admin/transactions.item.delete_description')</p>
                </div>

                <div class="modal-card-foot">
                    <button class="button is-danger" wire:click="deleteTransaction()" wire:loading.attr="disabled">@lang('admin/transactions.item.delete_transaction')</button>
                    <button class="button" wire:click="$set('isDeleting', false)" wire:loading.attr="disabled">@lang('admin/transactions.item.cancel')</button>
                </div>
            </div>
        </div>
    @endif
</div>
