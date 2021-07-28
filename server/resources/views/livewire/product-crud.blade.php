<div class="container content">
    <h1 class="title is-spaced">Strepen</h1>

    <h2 class="subtitle">Products</h2>

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

    @if ($products->count() > 0)
        {{ $products->links() }}

        <div class="columns is-multiline">
            @foreach ($products as $product)
                @livewire('product-item', ['product' => $product], key($product->id))
            @endforeach
        </div>

        {{ $products->links() }}
    @else
        <p><i>No products found!</i></p>
    @endif

    <div class="box">
        <h3 class="subtitle">Create new product</h3>

        <form wire:submit.prevent="createProduct">
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
                    <textarea class="textarea @error('productDescription') is-danger @enderror" id="productDescription"
                        wire:model.defer="productDescription"></textarea>
                </div>
                @error('productDescription') <p class="help is-danger">{{ $message }}</p> @enderror
            </div>

            <div>
                <button type="submit" class="button is-info">Create new product</button>
            </div>
        </form>
    </div>
</div>
