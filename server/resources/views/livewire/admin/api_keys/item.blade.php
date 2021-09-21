<div class="column is-one-third">
    <div class="card" style="display: flex; flex-direction: column; height: 100%; margin-bottom: 0; overflow: hidden;">
        <div class="card-content content" style="flex: 1; margin-bottom: 0;">
            <h4>
                {{ $apiKey->name }}
                <span class="is-pulled-right">
                    @if ($apiKey->level == App\Models\ApiKey::LEVEL_REQUIRE_AUTH)
                        <span class="tag is-success">{{ Str::upper(__('admin/api_keys.item.level_require_auth_tag')) }}</span>
                    @endif
                    @if ($apiKey->level == App\Models\ApiKey::LEVEL_NO_AUTH)
                        <span class="tag is-danger">{{ Str::upper(__('admin/api_keys.item.level_no_auth_tag')) }}</span>
                    @endif
                    @if (!$apiKey->active)
                        <span class="tag is-warning">{{ Str::upper(__('admin/api_keys.item.inactive')) }}</span>
                    @endif
                </span>
            </h4>
            <p>@lang('admin/api_keys.item.key'): <code>{{ $apiKey->key }}</code></p>
            <p>@lang('admin/api_keys.item.requests'): <b>{{ $apiKey->requests }}</b></p>
        </div>

        <div class="card-footer">
            <a href="#" class="card-footer-item" wire:click.prevent="$set('isEditing', true)">@lang('admin/api_keys.item.edit')</a>
            <a href="#" class="card-footer-item has-text-danger" wire:click.prevent="$set('isDeleting', true)">@lang('admin/api_keys.item.delete')</a>
        </div>
    </div>

    @if ($isEditing)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isEditing', false)"></div>

            <form wire:submit.prevent="editApiKey" class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/api_keys.item.edit_api_key')</p>
                    <button type="button" class="delete" wire:click="$set('isEditing', false)"></button>
                </div>

                <div class="modal-card-body">
                    <div class="field">
                        <label class="label" for="name">@lang('admin/api_keys.item.name')</label>
                        <div class="control">
                            <input class="input @error('apiKey.name') is-danger @enderror" type="text" id="name"
                                wire:model.defer="apiKey.name" tabindex="1" required>
                        </div>
                        @error('apiKey.name') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label class="label" for="level">@lang('admin/api_keys.item.level')</label>
                        <div class="control">
                            <div class="select is-fullwidth @error('apiKey.level') is-danger @enderror">
                                <select id="level" wire:model.defer="apiKey.level">
                                    <option value="{{ App\Models\ApiKey::LEVEL_REQUIRE_AUTH }}">@lang('admin/api_keys.item.level_require_auth')</option>
                                    <option value="{{ App\Models\ApiKey::LEVEL_NO_AUTH }}">@lang('admin/api_keys.item.level_no_auth')</option>
                                 </select>
                            </div>
                        </div>
                        @error('apiKey.level') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label class="label" for="active">@lang('admin/api_keys.item.active')</label>
                        <label class="checkbox" for="active">
                            <input type="checkbox" id="active" wire:model.defer="apiKey.active">
                            @lang('admin/api_keys.item.active_api_key')
                        </label>
                    </div>
                </div>

                <div class="modal-card-foot">
                    <button type="submit" class="button is-link">@lang('admin/api_keys.item.edit_api_key')</button>
                    <button type="button" class="button" wire:click="$set('isEditing', false)" wire:loading.attr="disabled">@lang('admin/api_keys.item.cancel')</button>
                </div>
            </form>
        </div>
    @endif

    @if ($isDeleting)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isDeleting', false)"></div>

            <div class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/api_keys.item.delete_api_key')</p>
                    <button type="button" class="delete" wire:click="$set('isDeleting', false)"></button>
                </div>

                <div class="modal-card-body">
                    <p>@lang('admin/api_keys.item.delete_description')</p>
                </div>

                <div class="modal-card-foot">
                    <button class="button is-danger" wire:click="deleteApiKey()" wire:loading.attr="disabled">@lang('admin/api_keys.item.delete_api_key')</button>
                    <button class="button" wire:click="$set('isDeleting', false)" wire:loading.attr="disabled">@lang('admin/api_keys.item.cancel')</button>
                </div>
            </div>
        </div>
    @endif
</div>
