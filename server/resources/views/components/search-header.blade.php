<div class="columns">
    <div class="column is-one-third">
        {{ $slot }}
    </div>
    <div class="column is-two-thirds">
        <form wire:submit.prevent="search">
            <div class="field has-addons">
                <div class="control" style="width: 100%;">
                    <input class="input" type="text" wire:model.defer="query" placeholder="@lang('components.search_header.query', ['item.name' => $itemName])">
                </div>
                @if (isset($fields))
                    {{ $fields }}
                @endif
                <div class="control">
                    <button class="button is-link" type="submit">@lang('components.search_header.search')</button>
                </div>
            </div>
        </form>
    </div>
</div>
