<div class="modal is-active">
    <div class="modal-background" wire:click="closeCreated"></div>

    <div class="modal-card">
        <div class="modal-card-head">
            <p class="modal-card-title">@lang('components.transaction_created_modal_header')</p>
            <button type="button" class="delete" wire:click="closeCreated"></button>
        </div>

        <div class="modal-card-body">
            <div class="box" style="width: 50%; margin: 0 auto; margin-bottom: 1.5rem;">
                <div class="image is-square is-rounded" style="background-image: @if ($transaction->user->thanks != null) url(/storage/thanks/{{ $transaction->user->thanks }}) @else url(/storage/thanks/{{ App\Models\Setting::get('default_user_thanks') }}) @endif;"></div>
            </div>

            <h2 class="title" style="margin-bottom: 1.5rem; text-align: center;">@lang('components.transaction_created_modal_thx')</h2>

            <div style="max-width: 24rem; margin-left: auto; margin-right: auto; margin-bottom: 1rem;">
                <x-products-amounts :products="$transaction->products()->orderByRaw('LOWER(name)')->get()" :totalPrice="$transaction->price" />
            </div>
        </div>

        <div class="modal-card-foot">
            <button type="button" class="button is-link is-fullwidth p-5" " wire:click="closeCreated" wire:loading.attr="disabled">@lang('components.transaction_created_modal_close')</button>
        </div>
    </div>

    <script>window.scrollTo({top: 0, behavior: 'smooth'});</script>
</div>
