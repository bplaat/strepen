<div class="container">
    <h2 class="title is-4">@lang('admin/products.crud.header')</h2>

    @component('components.search-header', ['itemName' => __('admin/products.crud.products')])
        <div class="buttons">
            <button class="button is-link" wire:click="$set('isCreating', true)" wire:loading.attr="disabled">@lang('admin/products.crud.create_product')</button>
        </div>
    @endcomponent

    @if ($products->count() > 0)
        {{ $products->links() }}

        <div class="columns is-multiline">
            @foreach ($products as $product)
                @livewire('admin.products.item', ['product' => $product], key($product->id))
            @endforeach
        </div>

        {{ $products->links() }}
    @else
        <p><i>@lang('admin/products.crud.empty')</i></p>
    @endif

    @if ($isCreating)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isCreating', false)"></div>

            <form wire:submit.prevent="createProduct" class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/products.crud.create_product')</p>
                    <button type="button" class="delete" wire:click="$set('isCreating', false)"></button>
                </div>

                <div class="modal-card-body">
                    <div class="field">
                        <label class="label" for="name">@lang('admin/products.crud.name')</label>
                        <div class="control">
                            <input class="input @error('product.name') is-danger @enderror" type="text" id="name"
                                wire:model.defer="product.name" required>
                        </div>
                        @error('product.name') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label class="label" for="price">@lang('admin/products.crud.price')</label>
                        <p class="control has-icons-left">
                            <input class="input @error('product.price') is-danger @enderror" type="number" step="0.01" id="price"
                                wire:model.defer="product.price" required>
                            <span class="icon is-small is-left">&euro;</span>
                        </p>
                        @error('product.price') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label class="label" for="description">@lang('admin/products.crud.description')</label>
                        <div class="control">
                            <textarea class="textarea is-family-monospace has-fixed-size @error('product.description') is-danger @enderror" id="description"
                                wire:model.defer="product.description"></textarea>
                        </div>
                        @error('product.description') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label class="label" for="image">@lang('admin/products.crud.image')</label>
                        <div class="control">
                            <input class="input @error('image') is-danger @enderror" type="file" accept=".jpg,.jpeg,.png"
                                id="image" wire:model="image">
                        </div>
                        @error('image')
                            <p class="help is-danger">{{ $message }}</p>
                        @else
                            <p class="help">@lang('admin/products.crud.image_help')</p>
                        @enderror
                    </div>
                </div>

                <div class="modal-card-foot">
                    <button type="submit" class="button is-link">@lang('admin/products.crud.create_product')</button>
                    <button type="button" class="button" wire:click="$set('isCreating', false)" wire:loading.attr="disabled">@lang('admin/products.crud.cancel')</button>
                </div>
            </form>
        </div>
    @endif
</div>
