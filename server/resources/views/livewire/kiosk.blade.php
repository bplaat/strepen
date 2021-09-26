<div class="container">
    <h1 class="title is-4">@lang('kiosk.header')</h1>

    <form id="mainForm" wire:submit.prevent="createTransaction"></form>

    @livewire('components.user-chooser')

    <div class="field">
        <label class="label" for="name">@lang('kiosk.name')</label>
        <div class="control">
            <input class="input @error('transaction.name') is-danger @enderror" type="text" id="name"
                form="mainForm" wire:model.defer="transaction.name" required>
        </div>
        @error('transaction.name') <p class="help is-danger">{{ $message }}</p> @enderror
    </div>

    @livewire('components.products-chooser', ['selectedProducts' => $selectedProducts])

    <div class="field">
        <div class="control">
            <button type="submit" form="mainForm" class="button is-link">@lang('kiosk.create_transaction')</button>
        </div>
    </div>

    @if ($isCreated)
        <div class="modal is-active">
            <div class="modal-background" wire:click="closeCreated"></div>

            <form wire:submit.prevent="createPost" class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">@lang('kiosk.created_header')</p>
                    <button type="button" class="delete" wire:click="closeCreated"></button>
                </div>

                <div class="modal-card-body">
                    <div class="box" style="width: 50%; margin: 0 auto; margin-bottom: 24px;">
                        <div style="background-image: @if (Auth::user()->thanks != null) url(/storage/thanks/{{ Auth::user()->thanks }}) @else url(/images/thanks/default.gif) @endif;
                            background-size: cover; background-position: center center; padding-top: 100%; border-radius: 6px;"></div>
                    </div>

                    <h2 class="title" style="margin-bottom: 24px; text-align: center;">@lang('kiosk.thx')</h2>


                    @foreach ($selectedProducts as $selectedProduct)
                        <div class="media" style="display: flex; align-items: center; width: 75%; margin-left: auto; margin-right: auto;">
                            <div class="media-left">
                                <div style="width: 64px; height: 64px; border-radius: 6px; background-size: cover; background-position: center center;
                                    background-image: url({{ $selectedProduct['product']['image'] != null ? '/storage/products/' . $selectedProduct['product']['image'] : '/images/products/unkown.png' }});"></div>
                            </div>
                            <div class="media-content">
                                <p>
                                    <b>{{ $selectedProduct['product']['name'] }}</b>
                                    (@component('components.money-format', ['money' => $selectedProduct['product']['price']])@endcomponent)
                                </p>
                                <p>
                                    @component('components.amount-format', ['amount' => $selectedProduct['amount']])@endcomponent
                                </p>
                            </div>
                            <div class="media-right">
                                <p>
                                    @component('components.money-format', ['money' => $selectedProduct['product']['price'] * $selectedProduct['amount']])@endcomponent
                                </p>
                            </div>
                        </div>
                    @endforeach

                    <div class="media" style="display: flex; align-items: center; width: 75%; margin-left: auto; margin-right: auto; margin-bottom: 16px;">
                        <div class="media-left">
                            <div style="width: 64px;"></div>
                        </div>
                        <div class="media-content">
                            <p>
                                @component('components.amount-format', ['amount' => $selectedProducts->pluck('amount')->sum()])@endcomponent
                            </p>
                        </div>
                        <div class="media-right">
                            <p>
                            @component('components.money-format', ['money' => $transaction->price])@endcomponent
                            </p>
                        </div>
                    </div>
                </div>

                <div class="modal-card-foot">
                    <button type="button" class="button is-link" style="margin: 0 auto;" wire:click="closeCreated" wire:loading.attr="disabled">@lang('kiosk.close')</button>
                </div>
            </form>
        </div>
    @endif
</div>
