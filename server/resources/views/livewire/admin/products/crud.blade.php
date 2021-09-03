<div>
    <h2 class="title is-4">@lang('admin/products.crud.header')</h2>

    <div class="columns">
        <div class="column">
            <div class="buttons">
                <button class="button is-link" wire:click="$set('isCreating', true)">@lang('admin/products.crud.create_product')</button>
            </div>
        </div>

        <div class="column">
            <form wire:submit.prevent="refresh">
                <div class="field has-addons">
                    <div class="control" style="width: 100%;">
                        <input class="input" type="text" id="q" wire:model.defer="q" placeholder="@lang('admin/products.crud.query')">
                    </div>
                    <div class="control">
                        <button class="button is-link" type="submit">@lang('admin/products.crud.search')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

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
                <header class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/products.crud.create_product')</p>
                    <button type="button" class="delete" wire:click="$set('isCreating', false)"></button>
                </header>

                <section class="modal-card-body">
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
                            <textarea class="textarea has-fixed-size @error('product.description') is-danger @enderror" id="description"
                                wire:model.defer="product.description"></textarea>
                        </div>
                        @error('product.description') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label class="label" for="image">@lang('admin/products.crud.image')</label>
                        <div class="control">
                            <input class="input @error('productImage') is-danger @enderror" type="file" accept=".jpg,.jpeg,.png"
                                id="image" wire:model="productImage">
                        </div>
                        @error('productImage')
                            <p class="help is-danger">{{ $message }}</p>
                        @else
                            <p class="help">@lang('admin/products.crud.image_help')</p>
                        @enderror
                    </div>
                </section>

                <footer class="modal-card-foot">
                    <button type="submit" class="button is-link">@lang('admin/products.crud.create_product')</button>
                    <button type="button" class="button" wire:click="$set('isCreating', false)">@lang('admin/products.crud.cancel')</button>
                </footer>
            </form>
        </div>
    @endif
</div>
