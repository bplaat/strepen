<div class="container">
    <h2 class="title">@lang('admin/transactions.crud.header')</h2>

    <x-search-header :itemName="__('admin/transactions.crud.transactions')">
        <div class="buttons">
            <button class="button is-link" wire:click="openCreateTransaction" wire:loading.attr="disabled">@lang('admin/transactions.crud.create_transaction')</button>
            <button class="button is-link" wire:click="openCreateDeposit" wire:loading.attr="disabled">@lang('admin/transactions.crud.create_deposit_small')</button>
            <button class="button is-link" wire:click="openCreateFood" wire:loading.attr="disabled">@lang('admin/transactions.crud.create_food_small')</button>
        </div>

        <x-slot name="fields">
            <x-transaction-type-chooser />

            <livewire:components.user-chooser :userId="$user_id" inline="true" includeStrepenUser="true" relationship="true" />

            <livewire:components.product-chooser :productId="$product_id" inline="true" relationship="true" />
        </x-slot>
    </x-search-header>

    @if ($transactions->count() > 0)
        {{ $transactions->links() }}

        <div class="columns is-multiline">
            @foreach ($transactions as $transaction)
                <livewire:admin.transactions.item :transaction="$transaction" :key="$transaction->id" />
            @endforeach
        </div>

        {{ $transactions->links() }}
    @else
        <p><i>@lang('admin/transactions.crud.empty')</i></p>
    @endif

    @if ($isCreatingTransaction)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isCreatingTransaction', false)"></div>

            <form id="mainForm" wire:submit.prevent="$emit('getSelectedProducts')"></form>

            <div class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/transactions.crud.create_transaction')</p>
                    <button type="button" class="delete" wire:click="$set('isCreatingTransaction', false)"></button>
                </div>

                <div class="modal-card-body">
                    <livewire:components.user-chooser includeStrepenUser="true" />

                    <div class="field">
                        <label class="label" for="name">@lang('admin/transactions.crud.name')</label>
                        <div class="control">
                            <input class="input @error('transaction.name') is-danger @enderror" type="text" id="name"
                                form="mainForm" wire:model.defer="transaction.name" required>
                        </div>
                        @error('transaction.name') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <livewire:components.products-chooser :selectedProducts="$selectedProducts" noMax="true" />
                </div>

                <div class="modal-card-foot">
                    <button type="submit" form="mainForm" class="button is-link" wire:loading.attr="disabled">@lang('admin/transactions.crud.create_transaction')</button>
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
                        <label class="label" for="name">@lang('admin/transactions.crud.name')</label>
                        <div class="control">
                            <input class="input @error('transaction.name') is-danger @enderror" type="text" id="name"
                                wire:model.defer="transaction.name" required>
                        </div>
                        @error('transaction.name') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="tabs is-fullwidth">
                        <ul>
                            <li @if ($creatingDepositTab == 'single') class="is-active" @endif>
                                <a href="#" wire:click.prevent="$set('creatingDepositTab', 'single')">@lang('admin/transactions.crud.single')</a>
                            </li>
                            <li @if ($creatingDepositTab == 'multiple') class="is-active" @endif>
                                <a href="#" wire:click.prevent="$set('creatingDepositTab', 'multiple')">@lang('admin/transactions.crud.multiple')</a>
                            </li>
                        </ul>
                    </div>

                    @if ($creatingDepositTab == 'single')
                        <livewire:components.user-chooser />

                        <div class="field">
                            <label class="label" for="amount">@lang('admin/transactions.crud.amount')</label>
                            <div class="control has-icons-left">
                                <input class="input @error('transaction.price') is-danger @enderror" type="number" step="0.01" id="amount"
                                    wire:model.defer="transaction.price" required>
                                <span class="icon is-small is-left">&euro;</span>
                            </div>
                            @error('transaction.price') <p class="help is-danger">{{ $message }}</p> @enderror
                        </div>
                    @endif

                    @if ($creatingDepositTab == 'multiple')
                        <table class="table is-fullwidth">
                            <thead>
                                <tr>
                                    <th>@lang('admin/transactions.crud.name')</th>
                                    <th>@lang('admin/transactions.crud.amount')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $index => $user)
                                    <tr>
                                        <td style="vertical-align: middle;">
                                            <div style="float: left; margin-right: 12px; width: 24px; height: 24px; border-radius: 50%; background-size: cover; background-position: center center;
                                                background-image: url(/storage/avatars/{{ $user->avatar != null ? $user->avatar : App\Models\Setting::get('default_user_avatar') }});"></div>
                                            <label for="user-amount-{{ $index }}">{{ $user->name }}</label>
                                        </td>
                                        <td>
                                            <div class="control has-icons-left">
                                                <input class="input @error('transaction.price') is-danger @enderror" type="number" step="0.01" id="user-amount-{{ $index }}"
                                                    wire:model.defer="userAmounts.{{ $index }}">
                                                <span class="icon is-small is-left">&euro;</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
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
                        <label class="label" for="name">@lang('admin/transactions.crud.name')</label>
                        <div class="control">
                            <input class="input @error('transaction.name') is-danger @enderror" type="text" id="name"
                                wire:model.defer="transaction.name" required>
                        </div>
                        @error('transaction.name') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="tabs is-fullwidth">
                        <ul>
                            <li @if ($creatingFoodTab == 'single') class="is-active" @endif>
                                <a href="#" wire:click.prevent="$set('creatingFoodTab', 'single')">@lang('admin/transactions.crud.single')</a>
                            </li>
                            <li @if ($creatingFoodTab == 'multiple') class="is-active" @endif>
                                <a href="#" wire:click.prevent="$set('creatingFoodTab', 'multiple')">@lang('admin/transactions.crud.multiple')</a>
                            </li>
                        </ul>
                    </div>

                    @if ($creatingFoodTab == 'single')
                        <livewire:components.user-chooser />

                        <div class="field">
                            <label class="label" for="amount">@lang('admin/transactions.crud.amount')</label>
                            <div class="control has-icons-left">
                                <input class="input @error('transaction.price') is-danger @enderror" type="number" step="0.01" id="amount"
                                    wire:model.defer="transaction.price" required>
                                <span class="icon is-small is-left">&euro;</span>
                            </div>
                            @error('transaction.price') <p class="help is-danger">{{ $message }}</p> @enderror
                        </div>
                    @endif

                    @if ($creatingFoodTab == 'multiple')
                        <table class="table is-fullwidth">
                            <thead>
                                <tr>
                                    <th>@lang('admin/transactions.crud.name')</th>
                                    <th>@lang('admin/transactions.crud.amount')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $index => $user)
                                    <tr>
                                        <td style="vertical-align: middle;">
                                            <div style="float: left; margin-right: 12px; width: 24px; height: 24px; border-radius: 50%; background-size: cover; background-position: center center;
                                                background-image: url(/storage/avatars/{{ $user->avatar != null ? $user->avatar : App\Models\Setting::get('default_user_avatar') }});"></div>
                                            <label for="user-amount-{{ $index }}">{{ $user->name }}</label>
                                        </td>
                                        <td>
                                            <div class="control has-icons-left">
                                                <input class="input @error('transaction.price') is-danger @enderror" type="number" step="0.01" id="user-amount-{{ $index }}"
                                                    wire:model.defer="userAmounts.{{ $index }}">
                                                <span class="icon is-small is-left">&euro;</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                <div class="modal-card-foot">
                    <button type="submit" class="button is-link">@lang('admin/transactions.crud.create_food')</button>
                    <button type="button" class="button" wire:click="$set('isCreatingFood', false)" wire:loading.attr="disabled">@lang('admin/transactions.crud.cancel')</button>
                </div>
            </form>
        </div>
    @endif
</div>
