@foreach ($products as $product)
    <div class="media" style="align-items: center;">
        <div class="media-left">
            <div class="image is-large is-rounded" style="background-image: url(/storage/products/{{ $product->image ?? App\Models\Setting::get('default_product_image') }});"></div>
        </div>
        <div class="media-content">
            <p class="mb-0"><b>{{ $product->name }}</b></p>
            <p class="has-text-grey">
                <span class="mr-2"><x-amount-format :amount="$product->pivot->amount" :isBold="false" /></span>
                @if ($product->pivot->price == null) ? @else <x-money-format :money="$product->pivot->price" :isBold="false" /> @endif
            </p>
        </div>
        <div class="media-right">
            <p>@if ($product->pivot->price == null) ? @else <x-money-format :money="$product->pivot->price * $product->pivot->amount" /> @endif</p>
        </div>
    </div>
@endforeach

<div class="media" style="align-items: center;">
    <div class="media-left">
        <div style="width: 3.5rem;"></div>
    </div>
    <div class="media-content">
        <p><x-amount-format :amount="$products->pluck('pivot.amount')->sum()" /></p>
    </div>
    <div class="media-right">
        <p><x-money-format :money="$totalPrice" /></p>
    </div>
</div>
