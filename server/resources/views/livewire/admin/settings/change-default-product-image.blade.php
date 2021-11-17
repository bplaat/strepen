<div>
    @if ($isChanged)
        <div class="notification is-success">
            <button class="delete" wire:click="$set('isChanged', false)"></button>
            <p>@lang('admin/settings.change_default_product_image.success_message')</p>
        </div>
    @endif

    @if ($isDeleted)
        <div class="notification is-warning">
            <button class="delete" wire:click="$set('isDeleted', false)"></button>
            <p>@lang('admin/settings.change_default_product_image.delete_message')</p>
        </div>
    @endif

    <form class="box" wire:submit.prevent="changeImage">
        <h2 class="title is-4">@lang('admin/settings.change_default_product_image.header')</h2>

        @if (App\Models\Setting::get('default_product_image') != 'default.png')
            <div class="field">
                <p>@lang('admin/settings.change_default_product_image.has_image')</p>
            </div>

            <div class="box" style="width: 75%;">
                <div class="image is-square is-rounded" style="background-image: url(/storage/products/{{ App\Models\Setting::get('default_product_image') }});"></div>
            </div>
        @else
            <div class="field">
                <p>@lang('admin/settings.change_default_product_image.no_image')</p>
            </div>

            <div class="box" style="width: 75%;">
                <div class="image is-square is-rounded" style="background-image: url(/storage/products/default.png);"></div>
            </div>
        @endif

        <div class="field">
            <label class="label" for="image">@lang('admin/settings.change_default_product_image.image')</label>

            <div class="control">
                <input class="input @error('image') is-danger @enderror" type="file" accept=".jpg,.jpeg,.png" id="image"
                    wire:model="image" required>

                @error('image')
                    <p class="help is-danger">{{ $message }}</p>
                @else
                    <p class="help">@lang('admin/settings.change_default_product_image.image_help')</p>
                @enderror
            </div>
        </div>

        <div class="field">
            <div class="control">
                <div class="buttons">
                    <button class="button is-link" type="submit">@lang('admin/settings.change_default_product_image.change_button')</button>
                    @if (App\Models\Setting::get('default_product_image') != 'default.png')
                        <a class="button is-danger" wire:click="deleteImage" wire:loading.attr="disabled">@lang('admin/settings.change_default_product_image.delete_button')</a>
                    @endif
                </div>
            </div>
        </div>
    </form>
</div>
