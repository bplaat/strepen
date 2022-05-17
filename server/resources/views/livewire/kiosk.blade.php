<div class="container">
    <h1 class="title">@lang('kiosk.header')</h1>

    <form class="box" wire:submit.prevent="createTransaction">
        <livewire:components.user-chooser name="user" sortBy="last_transaction" />

        <div class="field">
            <label class="label" for="name">@lang('kiosk.name')</label>
            <div class="control">
                <input class="input @error('transaction.name') is-danger @enderror" type="text" id="name"
                    wire:model.defer="transaction.name" required>
            </div>
            @error('transaction.name') <p class="help is-danger">{{ $message }}</p> @enderror
        </div>

        <livewire:components.products-chooser name="products" bigMode="true" sortBy="transactions_count" />

        <div class="field">
            <div class="control">
                <button type="submit" class="button is-link is-fullwidth p-5" wire:loading.attr="disabled">@lang('kiosk.create_transaction')</button>
            </div>
        </div>
    </form>

    @if ($isCreated)
        <x-transaction-created-modal :transaction="$transaction" />
    @endif
</div>
