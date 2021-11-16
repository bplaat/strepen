<div class="column is-one-third">
    <div class="card">
        <div class="card-content content">
            <h4>{{ $inventory->name }}</h4>
            <p><i>@lang('admin/inventories.item.created_by', ['user.name' => $inventory->user != null ? $inventory->user->name : '?', 'inventory.created_at' => $inventory->created_at->format('Y-m-d H:i')])</i></p>
            <p>@lang('admin/inventories.item.price'): <x-money-format :money="$inventory->price" /></p>

            @foreach ($inventory->products()->orderByRaw('LOWER(name)')->get() as $product)
                <p>
                    <div class="image is-small is-round is-inline" style="background-image: url(/storage/products/{{ $product->image != null ? $product->image : App\Models\Setting::get('default_product_image') }});"></div>
                    <b>{{ $product->name }}</b>: <x-amount-format :amount="$product->pivot->amount" />
                </p>
            @endforeach
        </div>

        <div class="card-footer">
            <a href="#" class="card-footer-item" wire:click.prevent="$set('isEditing', true)">@lang('admin/inventories.item.edit')</a>
            <a href="#" class="card-footer-item has-text-danger" wire:click.prevent="$set('isDeleting', true)">@lang('admin/inventories.item.delete')</a>
        </div>
    </div>

    @if ($isEditing)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isEditing', false)"></div>

            <form id="mainForm" wire:submit.prevent="$emit('getSelectedProducts')"></form>

            <div class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/inventories.item.edit_inventory')</p>
                    <button type="button" class="delete" wire:click="$set('isEditing', false)"></button>
                </div>

                <div class="modal-card-body">
                    <livewire:components.user-chooser :userId="$inventory->user_id" includeStrepenUser="true" validate="true" />

                    <div class="field">
                        <label class="label" for="name">@lang('admin/inventories.item.name')</label>
                        <div class="control">
                            <input class="input @error('inventory.name') is-danger @enderror" type="text" id="name"
                                form="mainForm" wire:model.defer="inventory.name" required>
                        </div>
                        @error('inventory.name') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="columns">
                        <div class="column">
                            <div class="field">
                                <label class="label" for="created_at_date">@lang('admin/inventories.item.created_at_date')</label>
                                <div class="control">
                                    <input class="input @error('createdAtDate') is-danger @enderror" type="date" id="created_at_date"
                                        form="mainForm" wire:model.defer="createdAtDate" required>
                                </div>
                                @error('createdAtDate') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="column">
                            <div class="field">
                                <label class="label" for="created_at_time">@lang('admin/inventories.item.created_at_time')</label>
                                <div class="control">
                                    <input class="input @error('createdAtTime') is-danger @enderror" type="time" step="1" id="created_at_time"
                                        form="mainForm" wire:model.defer="createdAtTime" required>
                                </div>
                                @error('createdAtTime') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <livewire:components.products-chooser :selectedProducts="$selectedProducts" noMax="true" validate="true" />
                </div>

                <div class="modal-card-foot">
                    <button type="submit" form="mainForm" class="button is-link" wire:loading.attr="disabled">@lang('admin/inventories.item.edit_inventory')</button>
                    <button type="button" class="button" wire:click="$set('isEditing', false)" wire:loading.attr="disabled">@lang('admin/inventories.item.cancel')</button>
                </div>
            </div>
        </div>
    @endif

    @if ($isDeleting)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isDeleting', false)"></div>

            <div class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/inventories.item.delete_inventory')</p>
                    <button type="button" class="delete" wire:click="$set('isDeleting', false)"></button>
                </div>

                <div class="modal-card-body">
                    <p>@lang('admin/inventories.item.delete_description')</p>
                </div>

                <div class="modal-card-foot">
                    <button class="button is-danger" wire:click="deleteInventory()" wire:loading.attr="disabled">@lang('admin/inventories.item.delete_inventory')</button>
                    <button class="button" wire:click="$set('isDeleting', false)" wire:loading.attr="disabled">@lang('admin/inventories.item.cancel')</button>
                </div>
            </div>
        </div>
    @endif
</div>
