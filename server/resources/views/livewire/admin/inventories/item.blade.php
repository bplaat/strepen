<div class="column is-one-third">
    <div class="card" style="display: flex; flex-direction: column; height: 100%; margin-bottom: 0; overflow: hidden;">
        <div class="card-content content" style="flex: 1; margin-bottom: 0;">
            <h4>{{ $inventory->name }}</h4>
            <p><i>@lang('admin/inventories.item.created_by', ['user.name' => $inventory->user->name, 'inventory.created_at' => $inventory->created_at->format('Y-m-d H:i')])</i></p>
            <p><strong>@lang('admin/inventories.item.price')</strong>: &euro; {{ number_format($inventory->price, 2, ',', '.') }}</p>
            <ul>
                @foreach ($inventory->products->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE) as $product)
                    <li><strong>{{ $product->name }}</strong>: {{ number_format($product->pivot->amount, 0, ',', '.') }}</li>
                @endforeach
            </ul>
        </div>

        <div class="card-footer">
            <a href="#" class="card-footer-item" wire:click.prevent="$set('isEditing', true)">@lang('admin/inventories.item.edit')</a>
            <a href="#" class="card-footer-item has-text-danger" wire:click.prevent="$set('isDeleting', true)">@lang('admin/inventories.item.delete')</a>
        </div>
    </div>

    @if ($isEditing)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isEditing', false)"></div>

            <form id="editInventory" wire:submit.prevent="editInventory"></form>

            <div class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/inventories.item.edit_inventory')</p>
                    <button type="button" class="delete" wire:click="$set('isEditing', false)"></button>
                </header>

                <section class="modal-card-body">
                    <div class="field">
                        <label class="label" for="user_id">@lang('admin/inventories.item.user')</label>
                        <div class="control">
                            <div class="select is-fullwidth @error('inventory.user_id') is-danger @enderror">
                                <select id="user_id" form="editInventory" wire:model.defer="inventory.user_id">
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @error('inventory.user_id') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label class="label" for="name">@lang('admin/inventories.item.name')</label>
                        <div class="control">
                            <input class="input @error('inventory.name') is-danger @enderror" type="text" id="name"
                                form="editInventory" wire:model.defer="inventory.name" required>
                        </div>
                        @error('inventory.name') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="columns">
                        <div class="column">
                            <div class="field">
                                <label class="label" for="created_at_date">@lang('admin/inventories.item.created_at_date')</label>
                                <div class="control">
                                    <input class="input @error('createdAtDate') is-danger @enderror" type="date" id="created_at_date"
                                        form="editInventory" wire:model.defer="createdAtDate" required>
                                </div>
                                @error('createdAtDate') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="column">
                            <div class="field">
                                <label class="label" for="created_at_time">@lang('admin/inventories.item.created_at_time')</label>
                                <div class="control">
                                    <input class="input @error('createdAtTime') is-danger @enderror" type="time" step="1" id="created_at_time"
                                        form="editInventory" wire:model.defer="createdAtTime" required>
                                </div>
                                @error('createdAtTime') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label" for="addProductId">@lang('admin/inventories.item.products')</label>
                        <div class="control">
                            <form wire:submit.prevent="addProduct">
                                <div class="field has-addons">
                                    <div class="control" style="width: 100%;">
                                        <div class="select is-fullwidth">
                                            <select id="addProductId" wire:model.defer="addProductId">
                                                <option value="null" disabled selected>@lang('admin/inventories.item.select_product')</option>
                                                @foreach ($products as $product)
                                                    @if (!$inventoryProducts->pluck('product_id')->contains($product->id))
                                                        <option value="{{ $product->id }}">{{ $product->name }} (&euro; {{ $product->price }})</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="control">
                                        <button class="button is-link" type="submit">@lang('admin/inventories.item.add_product')</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    @foreach ($inventoryProducts as $index => $inventoryProduct)
                        <div class="field">
                            <label class="label" for="amount{{ $index }}">
                                {{ $inventoryProduct['product']['name'] }} (&euro; {{ $inventoryProduct['product']['price'] }}) @lang('admin/inventories.item.amount')
                                <button type="button" class="delete is-pulled-right" wire:click="deleteProduct({{ $inventoryProduct['product_id'] }})"></button>
                            </label>
                            <div class="control">
                                <input class="input @error('inventoryProducts.{{ $index }}.amount') is-danger @enderror" type="number" min="1"
                                    id="amount{{ $index }}" form="editInventory" wire:model="inventoryProducts.{{ $index }}.amount" required>
                            </div>
                            @error('inventoryProducts.{{ $index }}.amount') <p class="help is-danger">{{ $message }}</p> @enderror
                        </div>
                    @endforeach
                </section>

                <footer class="modal-card-foot">
                    <button type="submit" form="editInventory" class="button is-link">@lang('admin/inventories.item.edit_inventory')</button>
                    <button type="button" class="button" wire:click="$set('isEditing', false)" wire:loading.attr="disabled">@lang('admin/inventories.item.cancel')</button>
                </footer>
            </div>
        </div>
    @endif

    @if ($isDeleting)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isDeleting', false)"></div>

            <div class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/inventories.item.delete_inventory')</p>
                    <button type="button" class="delete" wire:click="$set('isDeleting', false)"></button>
                </header>

                <section class="modal-card-body">
                    <p>@lang('admin/inventories.item.delete_description')</p>
                </section>

                <footer class="modal-card-foot">
                    <button class="button is-danger" wire:click="deleteInventory()" wire:loading.attr="disabled">@lang('admin/inventories.item.delete_inventory')</button>
                    <button class="button" wire:click="$set('isDeleting', false)" wire:loading.attr="disabled">@lang('admin/inventories.item.cancel')</button>
                </footer>
            </div>
        </div>
    @endif
</div>
