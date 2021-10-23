<div class="modal is-active">
    <div class="modal-background" wire:click="closeCreated"></div>

    <form wire:submit.prevent="createPost" class="modal-card">
        <div class="modal-card-head">
            <p class="modal-card-title">@lang('components.transaction_created_modal_header')</p>
            <button type="button" class="delete" wire:click="closeCreated"></button>
        </div>

        <div class="modal-card-body">
            <div class="box" style="width: 50%; margin: 0 auto; margin-bottom: 24px;">
                <div style="background-image: @if ($transaction->user->thanks != null) url(/storage/thanks/{{ $transaction->user->thanks }}) @else url(/storage/thanks/{{ App\Models\Setting::get('default_user_thanks') }}) @endif;
                    background-size: cover; background-position: center center; padding-top: 100%; border-radius: 6px;"></div>
            </div>

            <h2 class="title" style="margin-bottom: 24px; text-align: center;">@lang('components.transaction_created_modal_thx')</h2>

            @foreach ($transaction->products()->orderByRaw('LOWER(name)')->get() as $product)
                <div class="media" style="display: flex; align-items: center; max-width: 360px; margin-left: auto; margin-right: auto;">
                    <div class="media-left">
                        <div style="width: 64px; height: 64px; border-radius: 6px; background-size: cover; background-position: center center;
                            background-image: url(/storage/products/{{ $product->image != null ? $product->image : App\Models\Setting::get('default_product_image') }});"></div>
                    </div>
                    <div class="media-content">
                        <p>
                            <b>{{ $product->name }}</b>
                            (<x-money-format :money="$product->price" />)
                        </p>
                        <p><x-amount-format :amount="$product->pivot->amount" /></p>
                    </div>
                    <div class="media-right">
                        <p><x-money-format :money="$product->price * $product->pivot->amount" /></p>
                    </div>
                </div>
            @endforeach

            <div class="media" style="display: flex; align-items: center; max-width: 360px; margin-left: auto; margin-right: auto; margin-bottom: 16px;">
                <div class="media-left">
                    <div style="width: 64px;"></div>
                </div>
                <div class="media-content">
                    <p><x-amount-format :amount="$transaction->products->pluck('pivot.amount')->sum()" /></p>
                </div>
                <div class="media-right">
                    <p><x-money-format :money="$transaction->price" /></p>
                </div>
            </div>
        </div>

        <div class="modal-card-foot">
            <button type="button" class="button is-link" style="margin: 0 auto;" wire:click="closeCreated" wire:loading.attr="disabled">@lang('components.transaction_created_modal_close')</button>
        </div>
    </form>
</div>
