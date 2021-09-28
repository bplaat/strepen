@if (!$inline)
<div class="field">
    <label class="label" for="productName">@lang('components.product_chooser.product')</label>
@endif
    <div class="dropdown @if($isOpen) is-active @endif control" style="width: 100%;">
        <div class="dropdown-trigger control has-icons-left" style="width: 100%;">
            <input class="input" type="text" placeholder="@lang($relationship ? 'components.product_chooser.search_by_product' : 'components.product_chooser.search_product')"
                wire:model="productName" id="productName" autocomplete="off" wire:keydown.enter.prevent="selectFirstProduct"
                wire:focus="$set('isOpen', true)" wire:blur="$set('isOpen', false)">
            <span class="icon is-small is-left">
                <div style="width: 24px; height: 24px; border-radius: 4px; background-size: cover; background-position: center center;
                    background-image: url({{ $product != null && $product->image != null ? '/storage/products/' . $product->image : '/images/products/unkown.png' }});"></div>
            </span>
        </div>
        <div class="dropdown-menu" style="width: 100%;">
            <div class="dropdown-content">
                @if ($filteredProducts->count() > 0)
                    @foreach ($filteredProducts as $product)
                        <a href="#" wire:click.prevent="selectProduct({{ $product->id }})" class="dropdown-item" style="display: flex; align-items: center;">
                            <div style="margin-right: 12px; width: 24px; height: 24px; border-radius: 4px; background-size: cover; background-position: center center;
                                background-image: url({{ $product->image != null ? '/storage/products/' . $product->image : '/images/products/unkown.png' }});"></div>
                            <div style="flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {!! $productName != '' ? str_replace(' ', '&nbsp;', preg_replace('/(' . preg_quote($productName) . ')/i', '<b>$1</b>', $product->name)) : $product->name !!}
                            </div>
                        </a>
                    @endforeach
                @else
                    <div class="dropdown-item"><i>@lang('components.product_chooser.empty')</i></div>
                @endif
            </div>
        </div>
    </div>
@if (!$inline)
</div>
@endif
