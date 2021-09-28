<div class="control" style="width: 100%;">
    <div class="select is-fullwidth">
        <select id="type" wire:model.defer="role">
            <option value="">@lang('components.user_role_chooser_all')</option>
            <option value="normal">@lang('components.user_role_chooser_normal')</option>
            <option value="admin">@lang('components.user_role_chooser_admin')</option>
        </select>
    </div>
</div>
