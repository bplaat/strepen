<div class="container">
    <h2 class="title is-4">@lang('admin/inventories.crud.header')</h2>

    @component('components.search-header', ['itemName' => __('admin/inventories.crud.inventories')])
        <div class="buttons">
            <button class="button is-link" wire:click="$set('isCreating', true)" wire:loading.attr="disabled">@lang('admin/inventories.crud.create_inventory')</button>
        </div>
    @endcomponent

    @if ($inventories->count() > 0)
        {{ $inventories->links() }}

        <div class="columns is-multiline">
            @foreach ($inventories as $inventory)
                @livewire('admin.inventories.item', ['inventory' => $inventory], key($inventory->id))
            @endforeach
        </div>

        {{ $inventories->links() }}
    @else
        <p><i>@lang('admin/inventories.crud.empty')</i></p>
    @endif

    @if ($isCreating)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isCreating', false)"></div>

            <form id="createInventory" wire:submit.prevent="createInventory"></form>

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
                                form="createInventory" wire:model.defer="inventory.name" required>
                        </div>
                        @error('inventory.name') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label class="label" for="addProductId">@lang('admin/inventories.crud.products')</label>
                        <div class="control">
                            <form wire:submit.prevent="addProduct">
                                <div class="field has-addons">
                                    <div class="control" style="width: 100%;">
                                        <div class="select is-fullwidth">
                                            <select id="addProductId" wire:model.defer="addProductId">
                                                <option value="null" disabled selected>@lang('admin/inventories.crud.select_product')</option>
                                                @foreach ($products as $product)
                                                    @if (!$inventoryProducts->pluck('product_id')->contains($product->id))
                                                        <option value="{{ $product->id }}">{{ $product->name }} (&euro; {{ $product->price }})</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="control">
                                        <button class="button is-link" type="submit">@lang('admin/inventories.crud.add_product')</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="field">
                        @foreach ($inventoryProducts as $index => $inventoryProduct)
                            <div class="media" style="display: flex; align-items: center;">
                                <div class="media-left">
                                    <div style="width: 64px; height: 64px; background-size: cover; background-position: center center;
                                        background-image: url({{ $inventoryProduct['product']['image'] != null ? '/storage/products/' . $inventoryProduct['product']['image'] : '/images/products/unkown.png' }});"></div>
                                </div>
                                <div class="media-content">
                                    <label class="label" for="amount{{ $index }}">
                                        {{ $inventoryProduct['product']['name'] }} (&euro; {{ $inventoryProduct['product']['price'] }}) @lang('admin/inventories.crud.amount'):
                                        <button type="button" class="delete is-pulled-right" wire:click="deleteProduct({{ $inventoryProduct['product_id'] }})"></button>
                                    </label>
                                    <div class="control">
                                        <input class="input @error('inventoryProducts.{{ $index }}.amount') is-danger @enderror" type="number"
                                            min="1" id="amount{{ $index }}" form="createInventory"
                                            wire:model="inventoryProducts.{{ $index }}.amount" required>
                                    </div>
                                    @error('inventoryProducts.{{ $index }}.amount') <p class="help is-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="modal-card-foot">
                    <button type="submit" form="createInventory" class="button is-link">@lang('admin/inventories.crud.create_inventory')</button>
                    <button type="button" class="button" wire:click="$set('isCreating', false)" wire:loading.attr="disabled">@lang('admin/inventories.crud.cancel')</button>
                </div>
            </div>
        </div>
    @endif
</div>
