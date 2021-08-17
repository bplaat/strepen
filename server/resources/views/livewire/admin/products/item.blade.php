<div class="column is-one-third">
    <div class="box content" style="height: 100%;">
        <h3 class="is-3">{{ $product->name }}: &euro; {{ $product->price }}</h3>
        @if ($product->description != null)
            <p><i>{{ $product->description }}</i></p>
        @endif
        <p><b>Amount in stock: {{ $product->amount }}</b></p>

        <div class="buttons">
            <button type="button" class="button is-link" wire:click="$set('isEditing', true)">Edit</button>
            <button type="button" class="button is-danger" wire:click="$set('isDeleting', true)">Delete</button>
        </div>
    </div>

    @if ($isEditing)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isEditing', false)"></div>

            <form wire:submit.prevent="updateProduct" class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title">Update product</p>
                    <button type="button" class="delete" wire:click="$set('isEditing', false)"></button>
                </header>

                <section class="modal-card-body">
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
                            <textarea class="textarea has-fixed-size @error('product.description') is-danger @enderror" id="productDescription"
                                wire:model.defer="product.description"></textarea>
                        </div>
                        @error('product.description') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>
                </section>

                <footer class="modal-card-foot">
                    <button type="submit" class="button is-link">Update product</button>
                    <button type="button" class="button" wire:click="$set('isEditing', false)">Cancel</button>
                </footer>
            </form>
        </div>
    @endif

    @if ($isDeleting)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isDeleting', false)"></div>

            <div class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title">Delete product confirmation</p>
                    <button type="button" class="delete" wire:click="$set('isDeleting', false)"></button>
                </header>

                <section class="modal-card-body">
                    <p>Are you sure you want to delete this product?</p>
                </section>

                <footer class="modal-card-foot">
                    <button class="button is-danger" wire:click="deleteProduct()">Delete product</button>
                    <button class="button" wire:click="$set('isDeleting', false)">Cancel</button>
                </footer>
            </div>
        </div>
    @endif
</div>
