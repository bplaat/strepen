<div class="container">
    @if (session()->has('create_transaction_message'))
        <div class="notification is-success">
            <button class="delete" onclick="this.parentNode.parentNode.removeChild(this.parentNode);"></button>
            <p>{{ session('create_transaction_message') }}</p>
        </div>
    @endif

    <h1 class="title is-4">@lang('kiosk.header')</h1>

    <form id="mainForm" wire:submit.prevent="createTransaction"></form>

    <div class="field">
        <label class="label" for="user_id">@lang('kiosk.user')</label>
        <div class="control">
            <div class="select is-fullwidth @error('transaction.user_id') is-danger @enderror">
                <select id="user_id" form="mainForm" wire:model.defer="transaction.user_id">
                    <option value="null" disabled selected>@lang('kiosk.select_user')</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @error('transaction.user_id') <p class="help is-danger">{{ $message }}</p> @enderror
    </div>

    <div class="field">
        <label class="label" for="name">@lang('kiosk.name')</label>
        <div class="control">
            <input class="input @error('transaction.name') is-danger @enderror" type="text" id="name"
                form="mainForm" wire:model.defer="transaction.name" required>
        </div>
        @error('transaction.name') <p class="help is-danger">{{ $message }}</p> @enderror
    </div>

    @livewire('components.products-chooser', ['selectedProducts' => $selectedProducts])

    <div class="field">
        <div class="control">
            <button type="submit" form="mainForm" class="button is-link">@lang('kiosk.create_transaction')</button>
        </div>
    </div>
</div>
