<div class="column is-one-third">
    <div class="card" style="display: flex; flex-direction: column; height: 100%; margin-bottom: 0; overflow: hidden;">
        <div class="card-content content" style="flex: 1; margin-bottom: 0;">
            <h3 class="is-3">{{ $inventory->name }}</h3>
            <p><i>@lang('admin/inventories.item.created_by', ['user.name' => $inventory->user->name, 'inventory.created_at' => $inventory->created_at->format('Y-m-d H:i')])</i></p>
            <ul>
                @foreach ($inventory->products as $product)
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

            <form wire:submit.prevent="editInventory" class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/inventories.item.edit_inventory')</p>
                    <button type="button" class="delete" wire:click="$set('isEditing', false)"></button>
                </header>

                <section class="modal-card-body">
                    <div class="field">
                        <label class="label" for="name">@lang('admin/inventories.item.name')</label>
                        <div class="control">
                            <input class="input @error('inventory.name') is-danger @enderror" type="text" id="name"
                                wire:model.defer="inventory.name" required>
                        </div>
                        @error('inventory.name') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>
                </section>

                <footer class="modal-card-foot">
                    <button type="submit" class="button is-link">@lang('admin/inventories.item.edit_inventory')</button>
                    <button type="button" class="button" wire:click="$set('isEditing', false)">@lang('admin/inventories.item.cancel')</button>
                </footer>
            </form>
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
                    <button class="button is-danger" wire:click="deleteInventory()">@lang('admin/inventories.item.delete_inventory')</button>
                    <button class="button" wire:click="$set('isDeleting', false)">@lang('admin/inventories.item.cancel')</button>
                </footer>
            </div>
        </div>
    @endif
</div>
