@if (!$inline)
<div class="field">
    <label class="label" for="productName">@lang('components.product_chooser.product')</label>
@endif
    <div class="dropdown @if($isOpen) is-active @endif control" style="width: 100%;">
        <div class="dropdown-trigger control has-icons-left" style="width: 100%;">
            <input class="input @if (!$valid) is-danger @endif" type="text" placeholder="@lang($relationship ? 'components.product_chooser.search_by_product' : 'components.product_chooser.search_product')"
                wire:model="productName" id="productName" autocomplete="off" wire:keydown.enter.prevent="selectFirstProduct"
                wire:focus="$set('isOpen', true)" wire:blur.debounce.100ms="$set('isOpen', false)">
            <span class="icon is-small is-left">
                <div class="image is-small is-rounded" style="background-image: url(/storage/products/{{ $product != null && $product->image != null ? $product->image : App\Models\Setting::get('default_product_image') }});"></div>
            </span>
        </div>
        <div class="dropdown-menu" style="width: 100%;">
            <div class="dropdown-content">
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
    </div>
@if (!$inline)
    @if (!$valid) <p class="help is-danger">@lang('components.product_chooser.empty_error')</p> @endif
</div>
@endif
