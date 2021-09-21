<div class="container">
    <h2 class="title is-4">@lang('admin/api_keys.crud.header')</h2>

    @component('components.search-header', ['itemName' => __('admin/api_keys.crud.api_keys')])
        <div class="buttons">
            <button class="button is-link" wire:click="$set('isCreating', true)" wire:loading.attr="disabled">@lang('admin/api_keys.crud.create_api_key')</button>
        </div>
    @endcomponent

    @if ($apiKeys->count() > 0)
        {{ $apiKeys->links() }}

        <div class="columns is-multiline">
            @foreach ($apiKeys as $apiKey)
                @livewire('admin.api-keys.item', ['apiKey' => $apiKey], key($apiKey->id))
            @endforeach
        </div>

        {{ $apiKeys->links() }}
    @else
        <p><i>@lang('admin/api_keys.crud.empty')</i></p>
    @endif

    @if ($isCreating)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isCreating', false)"></div>

            <form wire:submit.prevent="createApiKey" class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/api_keys.crud.create_api_key')</p>
                    <button type="button" class="delete" wire:click="$set('isCreating', false)"></button>
                </div>

                <div class="modal-card-body">
                    <div class="field">
                        <label class="label" for="name">@lang('admin/api_keys.crud.name')</label>
                        <div class="control">
                            <input class="input @error('apiKey.name') is-danger @enderror" type="text" id="name"
                                wire:model.defer="apiKey.name" tabindex="1" required>
                        </div>
                        @error('apiKey.name') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label class="label" for="level">@lang('admin/api_keys.crud.level')</label>
                        <div class="control">
                            <div class="select is-fullwidth @error('apiKey.level') is-danger @enderror">
                                <select id="level" wire:model.defer="apiKey.level">
                                    <option value="{{ App\Models\ApiKey::LEVEL_REQUIRE_AUTH }}">@lang('admin/api_keys.crud.level_require_auth')</option>
                                    <option value="{{ App\Models\ApiKey::LEVEL_NO_AUTH }}">@lang('admin/api_keys.crud.level_no_auth')</option>
                                 </select>
                            </div>
                        </div>
                        @error('apiKey.level') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="modal-card-foot">
                    <button type="submit" class="button is-link">@lang('admin/api_keys.crud.create_api_key')</button>
                    <button type="button" class="button" wire:click="$set('isCreating', false)" wire:loading.attr="disabled">@lang('admin/api_keys.crud.cancel')</button>
                </div>
            </form>
        </div>
    @endif
</div>
