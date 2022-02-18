@component('layouts.app')
    @slot('title', __('admin/settings.title'))
    <div class="container">
        <h1 class="title">@lang('admin/settings.header')</h1>

        <div class="columns">
            <div class="column is-half">
                <livewire:admin.settings.change-settings />
            </div>

            <div class="column is-half">
                <livewire:admin.settings.change-default-product-image />
            </div>
        </div>

        <div class="columns">
            <div class="column is-half">
                <livewire:admin.settings.change-default-avatar />
            </div>

            <div class="column is-half">
                <livewire:admin.settings.change-default-thanks />
            </div>
        </div>
    </div>
@endcomponent
