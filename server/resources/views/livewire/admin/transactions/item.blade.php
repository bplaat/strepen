<div class="column is-one-quarter">
    <div class="card" style="display: flex; flex-direction: column; height: 100%; margin-bottom: 0; overflow: hidden;">
        <div class="card-content content" style="flex: 1; margin-bottom: 0;">
            <h3 class="is-3">{{ $transaction->name }}</h3>

            @if ($transaction->type == \App\Models\Transaction::TYPE_TRANSACTION)
                <p>TODO</p>
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

            <form wire:submit.prevent="editTransaction" class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/transactions.item.edit_transaction')</p>
                    <button type="button" class="delete" wire:click="$set('isEditing', false)"></button>
                </header>

                <section class="modal-card-body">
                    <div class="field">
                        <label class="label" for="name">@lang('admin/transactions.item.name')</label>
                        <div class="control">
                            <input class="input @error('transaction.name') is-danger @enderror" type="text" id="name"
                                wire:model.defer="transaction.name" required>
                        </div>
                        @error('transaction.name') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>
                </section>

                <footer class="modal-card-foot">
                    <button type="submit" class="button is-link">@lang('admin/transactions.item.edit_transaction')</button>
                    <button type="button" class="button" wire:click="$set('isEditing', false)" wire:loading.attr="disabled">@lang('admin/transactions.item.cancel')</button>
                </footer>
            </form>
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
