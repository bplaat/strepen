<div class="field">
    <div class="field">
        <label class="label" for="addProductId">@lang('components.products_chooser.products')</label>
        <div class="control">
            <form wire:submit.prevent="addProduct">
                <div class="field has-addons">
                    <div class="control" style="width: 100%;">
                        <div class="select is-fullwidth">
                            <select id="addProductId" wire:model.defer="addProductId">
                                <option value="null" disabled selected>@lang('components.products_chooser.select_product')</option>
                                @foreach ($products as $product)
                                    @if (!$selectedProducts->pluck('product_id')->contains($product->id))
                                        <option value="{{ $product->id }}">{{ $product->name }} (@component('components.money-format', ['money' => $product->price])@endcomponent)</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="control">
                        <button class="button is-link" type="submit">@lang('components.products_chooser.add_product')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @foreach ($selectedProducts as $index => $selectedProduct)
        <div class="media" style="display: flex; align-items: center;">
            <div class="media-left">
                <div style="width: 64px; height: 64px; background-size: cover; background-position: center center;
                    background-image: url({{ $selectedProduct['product']['image'] != null ? '/storage/products/' . $selectedProduct['product']['image'] : '/images/products/unkown.png' }});"></div>
            </div>
            <div class="media-content">
                <label class="label" for="product-amount-{{ $index }}" style="font-weight: normal;">
                    <b>{{ $selectedProduct['product']['name'] }}</b> (@component('components.money-format', ['money' => $selectedProduct['product']['price']])@endcomponent) <b>@lang('components.products_chooser.amount')</b>
                    <button type="button" class="delete is-pulled-right" wire:click="deleteProduct({{ $selectedProduct['product_id'] }})"></button>
                </label>
                <div class="control">
                    <input class="input @error('selectedProducts.{{ $index }}.amount') is-danger @enderror" type="number"
                        min="1" id="product-amount-{{ $index }}" form="mainForm"
                        wire:model="selectedProducts.{{ $index }}.amount" required>
                </div>
                @error('selectedProducts.{{ $index }}.amount') <p class="help is-danger">{{ $message }}</p> @enderror
            </div>
        </div>
    @endforeach
</div>
