<div class="columns">
    <div class="column">
        {{ $slot }}
    </div>
    <div class="column">
        <form wire:submit.prevent="$refresh">
            <div class="field has-addons">
                <div class="control" style="width: 100%;">
                    <input class="input" type="text" wire:model.defer="query" placeholder="@lang('components.search_header.query', ['item.name' => $itemName])">
                </div>
                <div class="control">
                    <button class="button is-link" type="submit">@lang('components.search_header.search')</button>
                </div>
            </div>
        </form>
    </div>
</div>
