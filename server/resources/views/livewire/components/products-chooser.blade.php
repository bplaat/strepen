<div class="field">
    <div class="field">
        <label class="label" for="productName">@lang('components.products_chooser.products')</label>
        <div class="control">
            <form wire:submit.prevent="addFirstProduct">
                <div class="field has-addons">
                    <div class="dropdown @if($isOpen) is-active @endif" style="width: 100%;">
                        <div class="dropdown-trigger control" style="width: 100%;">
                            <input class="input" type="text" placeholder="@lang('components.products_chooser.search_product')"
                                wire:model="productName" id="productName" autocomplete="off"
                                wire:focus="$set('isOpen', true)" wire:blur="$set('isOpen', false)">
                        </div>
                        <div class="dropdown-menu" style="width: 100%;">
                            <div class="dropdown-content">
                                @foreach ($filteredProducts as $product)
                                    <a href="#" wire:click.prevent="addProduct({{ $product->id }})" class="dropdown-item" style="display: flex; align-items: center;">
                                        <div style="margin-right: .75rem; width: 24px; height: 24px; border-radius: 3px; background-size: cover; background-position: center center;
                                            background-image: url({{ $product->image != null ? '/storage/products/' . $product->image : '/images/products/unkown.png' }});"></div>
                                        {!! $productName != '' ? str_replace(' ', '&nbsp;', preg_replace('/(' . preg_quote($productName) . ')/i', '<b>$1</b>', $product->name)) : $product->name !!}
                                    </a>
                                @endforeach
                            </div>
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
                <div style="width: 64px; height: 64px; border-radius: 6px; background-size: cover; background-position: center center;
                    background-image: url({{ $selectedProduct['product']['image'] != null ? '/storage/products/' . $selectedProduct['product']['image'] : '/images/products/unkown.png' }});"></div>
            </div>
            <div class="media-content">
                <label class="label" for="product-amount-{{ $index }}" style="font-weight: normal;">
                    <b>{{ $selectedProduct['product']['name'] }}</b> (@component('components.money-format', ['money' => $selectedProduct['product']['price']])@endcomponent) <b>@lang('components.products_chooser.amount')</b>
                    <button type="button" class="delete is-pulled-right" wire:click="deleteProduct({{ $selectedProduct['product_id'] }})"></button>
                </label>
                <div class="control">
                    <input class="input @error('selectedProducts.{{ $index }}.amount') is-danger @enderror" type="number"
                        min="1" @if (!$noMax) max="24" @endif id="product-amount-{{ $index }}" form="mainForm"
                        wire:model="selectedProducts.{{ $index }}.amount" required>
                </div>
                @error('selectedProducts.{{ $index }}.amount') <p class="help is-danger">{{ $message }}</p> @enderror
            </div>
        </div>
    @endforeach
</div>
