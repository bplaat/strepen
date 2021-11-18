<div class="field @if ($isBigMode) mb-5 @endif">
    @if ($isBigMode)
        <label class="label" for="productName">@lang('components.products_chooser.products')</label>

        @if (!$isValid)
            <div class="notification is-danger">
                <button class="delete" wire:click="$set('isValid', true)"></button>
                <p>@lang('components.products_chooser.empty_error')</p>
            </div>
        @endif

        @if ($isMinor)
            <div class="field">
                <p class="help">@lang('components.products_chooser.minor')</p>
            </div>
        @endif

        <div class="columns is-multiline">
            @foreach ($selectedProducts as $index => $selectedProduct)

                <div class="column is-one-quarter">
                    <div class="card">
                        <div class="card-image">
                            <div class="image is-square" style="background-image: url(/storage/products/{{ $selectedProduct['product']['image'] ?? App\Models\Setting::get('default_product_image') }});"></div>

                            @if ($selectedProduct['product']['alcoholic'])
                                <div class="card-image-tags">
                                    <span class="tag is-danger">{{ Str::upper(__('admin/products.item.alcoholic')) }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="card-content content">
                            <h4 class="mb-5" style="font-weight: 600;">
                                {{ $selectedProduct['product']['name'] }}: <x-money-format :money="$selectedProduct['product']['price']" :isBold="false" />
                            </h4>

                            <div class="columns is-mobile">
                                <div class="column">
                                    <button class="button is-link is-fullwidth" wire:click="decrementProductAmount({{ $selectedProduct['product_id'] }})">-</button>
                                </div>
                                <div class="column" style="display: flex; align-items: center; justify-content: center;">
                                    <h3 class="m-0">{{ $selectedProduct['amount'] }}</h3>
                                </div>
                                <div class="column">
                                    <button class="button is-link is-fullwidth" wire:click="incrementProductAmount({{ $selectedProduct['product_id'] }})">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="field">
            <label class="label" for="productName">@lang('components.products_chooser.products')</label>

            @if (!$isValid)
                <div class="notification is-danger">
                    <button class="delete" wire:click="$set('isValid', true)"></button>
                    <p>@lang('components.products_chooser.empty_error')</p>
                </div>
            @endif

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
                                            <a href="#" wire:click.prevent="addProduct({{ $product->id }})" class="dropdown-item">
                                                <div class="image is-small is-rounded is-inline" style="background-image: url(/storage/products/{{ $product->image ?? App\Models\Setting::get('default_product_image') }});"></div>
                                                {!! $productName != '' ? str_replace(' ', '&nbsp;', preg_replace('/(' . preg_quote($productName) . ')/i', '<b>$1</b>', $product->name)) : $product->name !!}
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
            <div class="media" style="align-items: center;">
                <div class="media-left">
                    <div class="image is-large is-rounded" style="background-image: url(/storage/products/{{ $selectedProduct['product']['image'] ?? App\Models\Setting::get('default_product_image') }});"></div>
                </div>
                <div class="media-content">
                    <label class="label" for="product-amount-{{ $index }}" style="font-weight: 600;">
                        {{ $selectedProduct['product']['name'] }} (<x-money-format :money="$selectedProduct['product']['price']" :isBold="false" />) <span class="is-hidden-mobile">@lang('components.products_chooser.amount')</span>
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
    @endif
</div>
