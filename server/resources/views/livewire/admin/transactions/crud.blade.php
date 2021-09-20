<div class="container">
    <h2 class="title is-4">@lang('admin/transactions.crud.header')</h2>

    @component('components.search-header', ['itemName' => __('admin/transactions.crud.transactions')])
        <div class="buttons">
            <button class="button is-link" wire:click="openCreateTransaction" wire:loading.attr="disabled">@lang('admin/transactions.crud.create_transaction')</button>
            <button class="button is-link" wire:click="openCreateDeposit" wire:loading.attr="disabled">@lang('admin/transactions.crud.create_deposit')</button>
            <button class="button is-link" wire:click="openCreateFood" wire:loading.attr="disabled">@lang('admin/transactions.crud.create_food')</button>
        </div>
    @endcomponent

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

            <form id="mainForm" wire:submit.prevent="createTransaction"></form>

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
                                <select id="user_id" form="mainForm" wire:model.defer="transaction.user_id">
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
                                form="mainForm" wire:model.defer="transaction.name" required>
                        </div>
                        @error('transaction.name') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    @livewire('components.products-chooser', ['selectedProducts' => $selectedProducts, 'nomax' => true])
                </div>

                <div class="modal-card-foot">
                    <button type="submit" form="mainForm" class="button is-link">@lang('admin/transactions.crud.create_transaction')</button>
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

    @if ($isCreatingFood)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isCreatingFood', false)"></div>

            <form wire:submit.prevent="createFood" class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/transactions.crud.create_food')</p>
                    <button type="button" class="delete" wire:click="$set('isCreatingFood', false)"></button>
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
                    <button type="submit" class="button is-link">@lang('admin/transactions.crud.create_food')</button>
                    <button type="button" class="button" wire:click="$set('isCreatingFood', false)" wire:loading.attr="disabled">@lang('admin/transactions.crud.cancel')</button>
                </div>
            </form>
        </div>
    @endif
</div>
