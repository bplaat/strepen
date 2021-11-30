<div class="container">
    <h2 class="title">@lang('admin/inventories.crud.header')</h2>

    <x-search-header :itemName="__('admin/inventories.crud.inventories')">
        <div class="buttons">
            <button class="button is-link" wire:click="$set('isCreating', true)" wire:loading.attr="disabled">@lang('admin/inventories.crud.create_inventory')</button>
        </div>

        <x-slot name="sorters">
            <option value="">@lang('admin/inventories.crud.created_at_desc')</option>
            <option value="created_at">@lang('admin/inventories.crud.created_at_asc')</option>
            <option value="name">@lang('admin/inventories.crud.name_asc')</option>
            <option value="name_desc">@lang('admin/inventories.crud.name_desc')</option>
            <option value="price_desc">@lang('admin/inventories.crud.price_desc')</option>
            <option value="price">@lang('admin/inventories.crud.price_asc')</option>
        </x-slot>

        <x-slot name="filters">
            <livewire:components.user-chooser name="user_filter" :userId="$user_id" includeInactive="true" inline="true" relationship="true" inventoriesRequired="true" />

            <livewire:components.product-chooser name="product_filter" :productId="$product_id" includeInactive="true" inline="true" relationship="true" />
        </x-slot>
    </x-search-header>

    @if ($inventories->count() > 0)
        {{ $inventories->links() }}

        <div class="columns is-multiline">
            @foreach ($inventories as $inventory)
                <livewire:admin.inventories.item :inventory="$inventory" :key="$inventory->id" />
            @endforeach
        </div>

        {{ $inventories->links() }}
    @else
        <p><i>@lang('admin/inventories.crud.empty')</i></p>
    @endif

    @if ($isCreating)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isCreating', false)"></div>

            <form id="mainForm" wire:submit.prevent="$emit('getSelectedProducts')"></form>

            <div class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/inventories.crud.create_inventory')</p>
                    <button type="button" class="delete" wire:click="$set('isCreating', false)"></button>
                </div>

                <div class="modal-card-body">
                    <div class="field">
                        <label class="label" for="name">@lang('admin/inventories.crud.name')</label>
                        <div class="control">
                            <input class="input @error('inventory.name') is-danger @enderror" type="text" id="name"
                                form="mainForm" wire:model.defer="inventory.name" required>
                        </div>
                        @error('inventory.name') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <livewire:components.products-chooser :selectedProducts="$selectedProducts" noMax="true" validate="true" />
                </div>

                <div class="modal-card-foot">
                    <button type="submit" form="mainForm" class="button is-link" wire:loading.attr="disabled">@lang('admin/inventories.crud.create_inventory')</button>
                    <button type="button" class="button" wire:click="$set('isCreating', false)" wire:loading.attr="disabled">@lang('admin/inventories.crud.cancel')</button>
                </div>
            </div>
        </div>
    @endif
</div>
