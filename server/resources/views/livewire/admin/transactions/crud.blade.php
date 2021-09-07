<div>
    <h2 class="title is-4">@lang('admin/transactions.crud.header')</h2>

    <div class="columns">
        <div class="column">
            <div class="buttons">
                <button class="button is-link" wire:click="$set('isCreatingTransaction', true)" wire:loading.attr="disabled">@lang('admin/transactions.crud.create_transaction')</button>
                <button class="button is-link" wire:click="$set('isCreatingDeposit', true)" wire:loading.attr="disabled">@lang('admin/transactions.crud.create_deposit')</button>
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

            <form wire:submit.prevent="createTransaction" class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/transactions.crud.create_transaction')</p>
                    <button type="button" class="delete" wire:click="$set('isCreatingTransaction', false)"></button>
                </header>

                <section class="modal-card-body">
                    <div class="field">
                        <label class="label" for="name">@lang('admin/transactions.crud.name')</label>
                        <div class="control">
                            <input class="input @error('transaction.name') is-danger @enderror" type="text" id="name"
                                wire:model.defer="transaction.name" required>
                        </div>
                        @error('transaction.name') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>
                </section>

                <footer class="modal-card-foot">
                    <button type="submit" class="button is-link">@lang('admin/transactions.crud.create_transaction')</button>
                    <button type="button" class="button" wire:click="$set('isCreatingTransaction', false)" wire:loading.attr="disabled">@lang('admin/transactions.crud.cancel')</button>
                </footer>
            </form>
        </div>
    @endif

    @if ($isCreatingDeposit)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isCreatingDeposit', false)"></div>

            <form wire:submit.prevent="createDeposit" class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/transactions.crud.create_deposit')</p>
                    <button type="button" class="delete" wire:click="$set('isCreatingDeposit', false)"></button>
                </header>

                <section class="modal-card-body">
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
                </section>

                <footer class="modal-card-foot">
                    <button type="submit" class="button is-link">@lang('admin/transactions.crud.create_transaction')</button>
                    <button type="button" class="button" wire:click="$set('isCreatingDeposit', false)" wire:loading.attr="disabled">@lang('admin/transactions.crud.cancel')</button>
                </footer>
            </form>
        </div>
    @endif
</div>
