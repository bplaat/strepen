<div class="column is-one-third">
    <div class="box content" style="height: 100%">
        @if ($isEditing)
            <form wire:submit.prevent="updateProduct">
                <h3 class="is-3">Update product</h3>

                <div class="field">
                    <label class="label" for="productName">Name</label>
                    <div class="control">
                        <input class="input @error('product.name') is-danger @enderror" type="text" id="productName"
                            wire:model.defer="product.name" required>
                    </div>
                    @error('product.name') <p class="help is-danger">{{ $message }}</p> @enderror
                </div>

                <div class="field">
                    <label class="label" for="productPrice">Price</label>
                    <p class="control has-icons-left">
                    <input class="input @error('product.price') is-danger @enderror" type="number" step="0.01" id="productPrice"
                        wire:model.defer="product.price" required>
                        <span class="icon is-small is-left">
                            &euro;
                        </span>
                    </p>
                    @error('product.price') <p class="help is-danger">{{ $message }}</p> @enderror
                </div>

                <div class="field">
                    <label class="label" for="productDescription">Description</label>
                    <div class="control">
                        <textarea class="textarea @error('product.description') is-danger @enderror" id="productDescription"
                            wire:model.defer="product.description"></textarea>
                    </div>
                    @error('product.description') <p class="help is-danger">{{ $message }}</p> @enderror
                </div>

                <div>
                    <button type="submit" class="button is-info">Update product</button>
                </div>
            </form>
        @else
            <h3 class="is-3">{{ $product->name }}: &euro; {{ $product->price }}</h3>
            @if ($product->description != null)
                <p><i>{{ $product->description }}</i></p>
            @endif
            <p><b>Amount in stock: {{ $product->amount }}</b></p>

            <div class="buttons">
                <button type="button" class="button is-info" wire:click="editProduct()">Edit</button>
                <button type="button" class="button is-danger" wire:click="deleteProduct()">Delete</button>
            </div>
       @endif
    </div>
</div>
