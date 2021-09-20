<div class="field">
    <div class="field">
        <label class="label" for="addProductName">@lang('components.products_chooser.products')</label>
        <div class="control">
            <form wire:submit.prevent="addProduct">
                <div class="field has-addons">
                    <div class="dropdown @if($isOpen) is-active @endif" style="width: 100%;">
                        <div class="dropdown-trigger control" style="width: 100%;">
                            <input class="input @error('addProductName') is-danger @enderror"
                                type="text" placeholder="@lang('components.products_chooser.search_product')"
                                wire:model="addProductName" wire:focus="$set('isOpen', true)" wire:blur="$set('isOpen', false)">
                        </div>
                        <div class="dropdown-menu" style="width: 100%;">
                            <div class="dropdown-content">
                                @foreach ($products as $product)
                                    @if (!$selectedProducts->pluck('product_id')->contains($product->id) && (strlen($addProductName) == 0 || stripos($product->name, $addProductName) !== false))
                                        <a href="#" wire:click.prevent="addProduct({{ $product->id }})" class="dropdown-item" style="display: flex; align-items: center;">
                                            <div style="margin-right: .75rem; width: 24px; height: 24px; background-size: cover; background-position: center center;
                                                background-image: url({{ $product->image != null ? '/storage/products/' . $product->image : '/images/products/unkown.png' }});"></div>
                                            {!! $addProductName != '' ? str_replace(' ', '&nbsp;', preg_replace('/(' . preg_quote($addProductName) . ')/i', '<b>$1</b>', $product->name)) : $product->name !!}
                                        </a>
                                    @endif
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
                        min="1" @if (!$nomax) max="24" @endif id="product-amount-{{ $index }}" form="mainForm"
                        wire:model="selectedProducts.{{ $index }}.amount" required>
                </div>
                @error('selectedProducts.{{ $index }}.amount') <p class="help is-danger">{{ $message }}</p> @enderror
            </div>
        </div>
    @endforeach
</div>
