<div class="field @if ($bigMode) mb-5 @endif">
    @if ($bigMode)
        <label class="label" for="productName">@lang('components.products_chooser.products')</label>

        @if (!$valid)
            <div class="notification is-danger">
                <button type="button" class="delete" wire:click="$set('valid', true)"></button>
                <p>@lang('components.products_chooser.empty_error')</p>
            </div>
        @endif

        @if ($minor)
            <div class="field">
                <p class="help">@lang('components.products_chooser.minor')</p>
            </div>
        @endif

        <div class="columns is-multiline">
            @foreach ($filteredProducts as $product)
                @php
                    $selectedProduct = $selectedProducts->firstWhere('product_id', $product->id);
                @endphp

                <div class="column is-one-quarter">
                    <div class="card">
                        <div class="card-image">
                            <div class="image is-square" style="background-image: url(/storage/products/{{ $product->image ?? App\Models\Setting::get('default_product_image') }});"></div>

                            @if ($product->alcoholic)
                                <div class="card-image-tags">
                                    <span class="tag is-danger">{{ Str::upper(__('admin/products.item.alcoholic')) }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="card-content content">
                            <h4 class="mb-5" style="font-weight: 600;">
                                {{ $product->name }}: <x-money-format :money="$product->price" :isBold="false" />
                            </h4>

                            <div class="columns is-mobile">
                                <div class="column">
                                    <button type="button" class="button is-link is-fullwidth" wire:click="decrementProductAmount({{ $product->id }})">-</button>
                                </div>
                                <div class="column" style="display: flex; align-items: center; justify-content: center;">
                                    <h3 class="m-0">{{ $selectedProduct != null ? $selectedProduct['amount'] : 0 }}</h3>
                                </div>
                                <div class="column">
                                    <button type="button" class="button is-link is-fullwidth" wire:click="incrementProductAmount({{ $product->id }})">+</button>
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

            @forelse ($selectedProducts as $index => $selectedProduct)
                @php
                    $product = $products->firstWhere('id', $selectedProduct['product_id']);
                @endphp

                <div class="media" style="align-items: center;">
                    <div class="media-left">
                        <div class="image is-large is-rounded" style="background-image: url(/storage/products/{{ $product->image ?? App\Models\Setting::get('default_product_image') }});"></div>
                    </div>
                    <div class="media-content">
                        <label class="label" for="product-amount-{{ $index }}" style="font-weight: 600;">
                            {{ $product->name }} (<x-money-format :money="$product->price" :isBold="false" />) <span class="is-hidden-mobile">@lang('components.products_chooser.amount')</span>
                            <button type="button" class="delete is-pulled-right" style="transform: translateY(3px);" wire:click="deleteProduct({{ $product->id }})"></button>
                        </label>
                        <div class="control">
                            <input class="input @if (!$valid) is-danger @endif" type="number"
                                min="1" @if (!$noMax) max="{{ App\Models\Setting::get('max_stripe_amount') }}" @endif id="product-amount-{{ $index }}"
                                wire:model="selectedProducts.{{ $index }}.amount" required>
                        </div>
                    </div>
                </div>
            @empty
                <p><i>@lang('components.products_chooser.products_help')</i></p>
            @endforelse
        </div>

        <div class="field">
            <div class="dropdown @if($isOpen) is-active @endif control" style="width: 100%;">
                <div class="dropdown-trigger control" style="width: 100%;">
                    <input class="input @if (!$valid) is-danger @endif" type="text" placeholder="@lang('components.products_chooser.search_product')"
                        wire:model="productName" id="productName" autocomplete="off" wire:keydown.enter.prevent="addFirstProduct"
                        wire:focus="$set('isOpen', true)" wire:blur.debounce.100ms="$set('isOpen', false)">
                </div>
                <div class="dropdown-menu" style="width: 100%;">
                    <div class="dropdown-content">
                        @forelse ($filteredProducts as $product)
                            <a wire:click.prevent="addProduct({{ $product->id }})" class="dropdown-item">
                                <div class="image is-small is-rounded is-inline" style="background-image: url(/storage/products/{{ $product->image ?? 'default.png' }});"></div>
                                {!! $productName != '' ? str_replace(' ', '&nbsp;', preg_replace('#(' . preg_quote($productName) . ')#i', '<b>$1</b>', $product->name)) : $product->name !!}
                            </a>
                        @empty
                            <div class="dropdown-item"><i>@lang('components.products_chooser.search_empty')</i></div>
                        @endforelse
                    </div>
                </div>
            </div>

            @if ($minor)
                <p class="help">@lang('components.products_chooser.minor')</p>
            @endif

            @if (!$valid)
                <p class="help is-danger">@lang('components.products_chooser.empty_error')</p>
            @endif
        </div>
    @endif
</div>
