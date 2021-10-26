<div class="field">
    <div class="field">
        <label class="label" for="productName">@lang('components.products_chooser.products')</label>
        <div class="control">
            <form wire:submit.prevent="addFirstProduct">
                <div class="field has-addons is-block-mobile">
                    <div class="dropdown @if($isOpen) is-active @endif" style="width: 100%;">
                        <div class="dropdown-trigger control" style="width: 100%;">
                            <input class="input" type="text" placeholder="@lang('components.products_chooser.search_product')"
                                wire:model="productName" id="productName" autocomplete="off"
                                wire:focus="$set('isOpen', true)" wire:blur="$set('isOpen', false)">
                        </div>
                        <div class="dropdown-menu" style="width: 100%;">
                            <div class="dropdown-content">
                                @if ($filteredProducts->count() > 0)
                                    @foreach ($filteredProducts as $product)
                                        <a href="#" wire:click.prevent="addProduct({{ $product->id }})" class="dropdown-item" style="display: flex; align-items: center;">
                                            <div style="margin-right: 12px; width: 24px; height: 24px; border-radius: 3px; background-size: cover; background-position: center center;
                                                background-image: url(/storage/products/{{ $product->image != null ? $product->image : App\Models\Setting::get('default_product_image') }});"></div>
                                            <div style="flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                {!! $productName != '' ? str_replace(' ', '&nbsp;', preg_replace('/(' . preg_quote($productName) . ')/i', '<b>$1</b>', $product->name)) : $product->name !!}
                                            </div>
                                        </a>
                                    @endforeach
                                @else
                                    <div class="dropdown-item"><i>@lang('components.products_chooser.empty')</i></div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="control">
                        <button class="button is-link" type="submit" style="width: 100%;">@lang('components.products_chooser.add_product')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if ($isMinor)
        <div class="field">
            <p class="help">@lang('components.products_chooser.minor')</p>
        </div>
    @endif

    @foreach ($selectedProducts as $index => $selectedProduct)
        <div class="media" style="display: flex; align-items: center;">
            <div class="media-left">
                <div style="width: 64px; height: 64px; border-radius: 6px; background-size: cover; background-position: center center;
                    background-image: url(/storage/products/{{ $selectedProduct['product']['image'] != null ? $selectedProduct['product']['image'] : App\Models\Setting::get('default_product_image') }});"></div>
            </div>
            <div class="media-content">
                <label class="label" for="product-amount-{{ $index }}" style="font-weight: normal;">
                    <b>{{ $selectedProduct['product']['name'] }}</b> (<x-money-format :money="$selectedProduct['product']['price']" />) <b class="is-hidden-mobile">@lang('components.products_chooser.amount')</b>
                    <button type="button" class="delete is-pulled-right" style="transform: translateY(3px);" wire:click="deleteProduct({{ $selectedProduct['product_id'] }})"></button>
                </label>
                <div class="control">
                    <input class="input @error('selectedProducts.{{ $index }}.amount') is-danger @enderror" type="number"
                        min="1" @if (!$noMax) max="{{ App\Models\Setting::get('max_stripe_amount') }}" @endif id="product-amount-{{ $index }}" form="mainForm"
                        wire:model="selectedProducts.{{ $index }}.amount" required>
                </div>
                @error('selectedProducts.{{ $index }}.amount') <p class="help is-danger">{{ $message }}</p> @enderror
            </div>
        </div>
    @endforeach
</div>
