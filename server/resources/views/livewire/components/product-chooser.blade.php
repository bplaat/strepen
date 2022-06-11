@if (!$inline)
<div class="field">
    <label class="label" for="productName">@lang('components.product_chooser.product')</label>
@endif
    <div @class(['dropdown', 'is-active' => $isOpen, 'control']) style="width: 100%;">
        <div class="dropdown-trigger control has-icons-left" style="width: 100%;">
            <input id="product-chooser-input-{{ $htmlInputId }}" @class(['input', 'is-danger' => !$valid]) type="text"
                placeholder="@lang($relationship ? 'components.product_chooser.search_by_product' : 'components.product_chooser.search_product')"
                id="productName" autocomplete="off" wire:model="productName" wire:focus="$set('isOpen', true)">
            <span class="icon is-small is-left">
                <div class="image is-small is-rounded" style="background-image: url(/storage/products/{{ $product != null && $product->image != null ? $product->image : App\Models\Setting::get('default_product_image') }});"></div>
            </span>
        </div>
        <div class="dropdown-menu" style="width: 100%;">
            <div id="product-chooser-dropdown-{{ $htmlInputId }}" class="dropdown-content">
                @forelse ($filteredProducts as $product)
                    <a wire:click.prevent="selectProduct({{ $product->id }})" class="dropdown-item" wire:key="{{ $product->id }}">
                        <div class="image is-small is-rounded is-inline" style="background-image: url(/storage/products/{{ $product->image ?? App\Models\Setting::get('default_product_image') }});"></div>
                        {!! $productName != '' ? str_replace(' ', '&nbsp;', preg_replace('#(' . preg_quote($productName) . ')#i', '<b>$1</b>', $product->name)) : $product->name !!}
                    </a>
                @empty
                    <div class="dropdown-item"><i>@lang('components.product_chooser.empty')</i></div>
                @endforelse
            </div>
        </div>

        <script>
            (function () {
                const productChooserInput = document.getElementById('product-chooser-input-{{ $htmlInputId }}');
                const productChooserDropdown = document.getElementById('product-chooser-dropdown-{{ $htmlInputId }}');
                let selectedItem = -1;
                productChooserInput.addEventListener('keydown', event => {
                    const items = productChooserDropdown.children;
                    if (event.key == 'Enter' || event.key == 'Tab') {
                        event.preventDefault();
                        if (selectedItem != -1) {
                            @this.selectProduct(items[selectedItem].getAttribute('wire:key'));
                        } else {
                            @this.selectFirstProduct();
                        }
                    }
                    else if (event.key == 'ArrowUp') {
                        event.preventDefault();
                        if (selectedItem != -1) items[selectedItem].classList.remove('is-active');
                        if (selectedItem > -1) {
                            selectedItem--;
                        } else {
                            selectedItem = items.length - 1;
                        }
                        if (selectedItem != -1) items[selectedItem].classList.add('is-active');
                    }
                    else if (event.key == 'ArrowDown') {
                        event.preventDefault();
                        if (selectedItem != -1) items[selectedItem].classList.remove('is-active');
                        if (selectedItem < items.length - 1) {
                            selectedItem++;
                        } else {
                            selectedItem = -1;
                        }
                        if (selectedItem != -1) items[selectedItem].classList.add('is-active');
                    }
                    else {
                        selectedItem = -1;
                    }
                });
                productChooserInput.addEventListener('blur', () => {
                    setTimeout(() => {
                        @this.$set('isOpen', false);
                        selectedItem = -1;
                    }, 100);
                });
            })();
        </script>
    </div>
@if (!$inline)
    @if (!$valid) <p class="help is-danger">@lang('components.product_chooser.empty_error')</p> @endif
</div>
@endif
