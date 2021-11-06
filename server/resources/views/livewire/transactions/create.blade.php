<div class="container">
    <h1 class="title">@lang('transactions.create.header')</h1>

    <div class="box">
        <form id="mainForm" wire:submit.prevent="$emit('getSelectedProducts')"></form>

        <div class="field">
            <label class="label" for="name">@lang('transactions.create.name')</label>
            <div class="control">
                <input class="input @error('transaction.name') is-danger @enderror" type="text" id="name"
                    form="mainForm" wire:model.defer="transaction.name" required>
            </div>
            @error('transaction.name') <p class="help is-danger">{{ $message }}</p> @enderror
        </div>

        <livewire:components.products-chooser :selectedProducts="$selectedProducts" :isMinor="Auth::user()->minor" isBigMode="true" validate="true" />

        <div class="field">
            <div class="control">
                <button type="submit" form="mainForm" class="button is-link is-fullwidth p-5" wire:loading.attr="disabled">@lang('transactions.create.create_transaction')</button>
            </div>
        </div>
    </div>

    @if ($isCreated)
        <x-transaction-created-modal :transaction="$transaction" />
    @endif
</div>
