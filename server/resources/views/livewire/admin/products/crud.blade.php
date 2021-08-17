<div class="container">
    <h2 class="title is-4">Products</h2>

    <div class="columns">
        <div class="column">
            <div class="buttons">
                <button class="button is-link" wire:click="$set('isCreating', true)">Create new product</button>
            </div>
        </div>

        <div class="column">
            <form wire:submit.prevent="searchProduct">
                <div class="field has-addons">
                    <div class="control" style="width: 100%;">
                        <input class="input" type="text" id="q" wire:model.defer="q" placeholder="Search for products...">
                    </div>
                    <div class="control">
                        <button class="button is-link" type="submit">Search</button>
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
        <p><i>No products found!</i></p>
    @endif

    @if ($isCreating)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isCreating', false)"></div>

            <form wire:submit.prevent="createProduct" class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title">Create new product</p>
                    <button type="button" class="delete" wire:click="$set('isCreating', false)"></button>
                </header>

                <section class="modal-card-body">
                    <div class="field">
                        <label class="label" for="productName">Name</label>
                        <div class="control">
                            <input class="input @error('productName') is-danger @enderror" type="text" id="productName"
                                wire:model.defer="productName" required>
                        </div>
                        @error('productName') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label class="label" for="productPrice">Price</label>
                        <p class="control has-icons-left">
                        <input class="input @error('productPrice') is-danger @enderror" type="number" step="0.01" id="productPrice"
                            wire:model.defer="productPrice" required>
                            <span class="icon is-small is-left">
                                &euro;
                            </span>
                        </p>
                        @error('productPrice') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label class="label" for="productDescription">Description</label>
                        <div class="control">
                            <textarea class="textarea has-fixed-size @error('productDescription') is-danger @enderror" id="productDescription"
                                wire:model.defer="productDescription"></textarea>
                        </div>
                        @error('productDescription') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>
                </section>

                <footer class="modal-card-foot">
                    <button type="submit" class="button is-link">Create new product</button>
                    <button type="button" class="button" wire:click="$set('isCreating', false)">Cancel</button>
                </footer>
            </form>
        </div>
    @endif
</div>
