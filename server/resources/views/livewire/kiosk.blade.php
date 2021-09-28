<div class="container">
    <h1 class="title is-4">@lang('kiosk.header')</h1>

    <form id="mainForm" wire:submit.prevent="$emit('getSelectedProducts')"></form>

    <livewire:components.user-chooser />

    <div class="field">
        <label class="label" for="name">@lang('kiosk.name')</label>
        <div class="control">
            <input class="input @error('transaction.name') is-danger @enderror" type="text" id="name"
                form="mainForm" wire:model.defer="transaction.name" required>
        </div>
        @error('transaction.name') <p class="help is-danger">{{ $message }}</p> @enderror
    </div>

    <livewire:components.products-chooser :selectedProducts="$selectedProducts" />

    <div class="field">
        <div class="control">
            <button type="submit" form="mainForm" class="button is-link">@lang('kiosk.create_transaction')</button>
        </div>
    </div>

    @if ($isCreated)
        <x-transaction-created-modal :transaction="$transaction" />
    @endif
</div>
