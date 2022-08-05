<div class="container">
    <h1 class="title">@lang('transactions.create.header')</h1>

    <form class="box" wire:submit.prevent="createTransaction">
        <div class="field">
            <label class="label" for="name">@lang('transactions.create.name')</label>
            <div class="control">
                <input class="input @error('transaction.name') is-danger @enderror" type="text" id="name"
                    wire:model.defer="transaction.name" required>
            </div>
            @error('transaction.name') <p class="help is-danger">{{ $message }}</p> @enderror
        </div>

        <livewire:components.products-chooser name="products" :minor="Auth::user()->minor" bigMode="true" />

        <div class="field">
            <div class="control">
                <button type="submit" class="button is-link is-fullwidth p-5" wire:loading.attr="disabled">@lang('transactions.create.create_transaction')</button>
            </div>
        </div>
    </form>

    @if ($isCreated)
        <x-transaction-created-modal :transaction="$transaction" />
    @endif

    <script>
        document.addEventListener('livewire:load', () => {
            window.addEventListener('keydown', event => {
                if (event.key == 'Enter') {
                    event.preventDefault();
                    @this.createTransaction();
                }
            });
        });
    </script>
</div>
