<div class="column is-one-quarter">
    <div class="card" style="display: flex; flex-direction: column; height: 100%; margin-bottom: 0; overflow: hidden;">
        <div class="card-image">
            <div class="image has-background-link" style="@if ($product->image != null) background-image: url(/storage/products/{{ $product->image }}); @endif
                background-size: cover; background-position: center center; padding-top: 100%;"></div>

            <div style="position: absolute; top: 8px; right: 8px;">
                @if ($product->alcoholic)
                    <span class="tag is-danger">{{ Str::upper(__('admin/products.item.alcoholic')) }}</span>
                @endif

                @if (!$product->active)
                    <span class="tag is-warning">{{ Str::upper(__('admin/products.item.inactive')) }}</span>
                @endif
            </div>
        </div>

        <div class="card-content content" style="flex: 1; margin-bottom: 0;">
            <h4 style="font-weight: normal;">
                <span style="font-weight: 600;">{{ $product->name }}</span>: <x-money-format :money="$product->price" />
            </h4>

            <p>@lang('admin/products.item.amount'): <x-amount-format :amount="$product->amount" /></p>
        </div>

        <div class="card-footer">
            <a href="#" class="card-footer-item" wire:click.prevent="$set('isShowing', true)">@lang('admin/products.item.show')</a>
            <a href="#" class="card-footer-item" wire:click.prevent="$set('isEditing', true)">@lang('admin/products.item.edit')</a>
            <a href="#" class="card-footer-item has-text-danger" wire:click.prevent="$set('isDeleting', true)">@lang('admin/products.item.delete')</a>
        </div>
    </div>

    @if ($isShowing)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isShowing', false)"></div>

            <div class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/products.item.show_product')</p>
                    <button type="button" class="delete" wire:click="$set('isShowing', false)"></button>
                </div>

                <div class="modal-card-body content">
                    <h1 class="title is-spaced is-4">
                        {{ $product->name }}

                        <span class="is-pulled-right is-hidden-touch">
                            @if ($product->alcoholic)
                                <span class="tag is-danger">{{ Str::upper(__('admin/products.item.alcoholic')) }}</span>
                            @endif

                            @if (!$product->active)
                                <span class="tag is-warning">{{ Str::upper(__('admin/products.item.inactive')) }}</span>
                            @endif
                        </span>
                    </h1>

                    <p class="is-display-touch is-hidden-desktop">
                        @if ($product->alcoholic)
                            <span class="tag is-danger">{{ Str::upper(__('admin/products.item.alcoholic')) }}</span>
                        @endif

                        @if (!$product->active)
                            <span class="tag is-warning">{{ Str::upper(__('admin/products.item.inactive')) }}</span>
                        @endif
                    </p>

                    <h2 class="subtitle is-5">@lang('admin/products.item.general_info')</h2>
                    <p>@lang('admin/products.item.price'): <x-money-format :money="$product->price" /></p>
                    <p>@lang('admin/products.item.amount'): <x-amount-format :amount="$product->amount" /></p>
                    @if ($product->description != null)
                        <p>@lang('admin/products.item.description'): <i>{{ $product->description }}</i></p>
                    @else
                        <p>@lang('admin/products.item.description'): @lang('admin/products.item.description_unkown')</p>
                    @endif

                    <h2 class="subtitle is-5">@lang('admin/products.item.amount_info')</h2>
                    <canvas id="amount_chart_canvas"></canvas>

                    <script>
                    new Chart(document.getElementById('amount_chart_canvas').getContext('2d'), {
                        type: 'line',
                        data: {
                            datasets: [{
                                label: 'Amount',
                                data: @json($product->getAmountChart()),
                                borderColor: getComputedStyle(document.querySelector('.is-link')).backgroundColor,
                                tension: 0.1
                            }]
                        },
                        options: {
                            animation: false
                        }
                    });
                    </script>
                </div>
            </div>
        </div>
    @endif

    @if ($isEditing)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isEditing', false)"></div>

            <form wire:submit.prevent="editProduct" class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/products.item.edit_product')</p>
                    <button type="button" class="delete" wire:click="$set('isEditing', false)"></button>
                </div>

                <div class="modal-card-body">
                    <div class="field">
                        <label class="label" for="name">@lang('admin/products.item.name')</label>
                        <div class="control">
                            <input class="input @error('product.name') is-danger @enderror" type="text" id="name"
                                wire:model.defer="product.name" required>
                        </div>
                        @error('product.name') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label class="label" for="price">@lang('admin/products.item.price')</label>
                        <div class="control has-icons-left">
                            <input class="input @error('product.price') is-danger @enderror" type="number" step="0.01" id="price"
                                wire:model.defer="product.price" required>
                            <span class="icon is-small is-left">&euro;</span>
                        </div>
                        @error('product.price') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label class="label" for="description">@lang('admin/products.item.description')</label>
                        <div class="control">
                            <textarea class="textarea is-family-monospace has-fixed-size @error('product.description') is-danger @enderror" id="description"
                                wire:model.defer="product.description"></textarea>
                        </div>
                        @error('product.description') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label class="label" for="image">@lang('admin/products.item.image')</label>
                        @if ($product->image != null)
                            <div class="box" style="width: 50%;">
                                <div style="background-image: url(/storage/products/{{ $product->image }}); background-size: cover; background-position: center center; padding-top: 100%; border-radius: 6px;"></div>
                            </div>
                        @endif
                    </div>

                    <div class="columns">
                        <div class="column">
                            <div class="field">
                                <div class="control">
                                    <input class="input @error('image') is-danger @enderror" type="file" accept=".jpg,.jpeg,.png"
                                        id="image" wire:model="image">
                                </div>
                                @error('image')
                                    <p class="help is-danger">{{ $message }}</p>
                                @else
                                    <p class="help">@lang('admin/products.item.image_help')</p>
                                @enderror
                            </div>
                        </div>

                        @if ($product->image != null)
                            <div class="column">
                                <div class="field">
                                    <div class="control">
                                        <button type="button" class="button is-danger" wire:click="deleteImage" wire:loading.attr="disabled">@lang('admin/products.item.delete_image')</button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="columns">
                        <div class="column">
                            <div class="field">
                                <label class="label" for="alcoholic">@lang('admin/products.item.alcoholic')</label>
                                <label class="checkbox" for="alcoholic">
                                    <input type="checkbox" id="alcoholic" wire:model.defer="product.alcoholic">
                                    @lang('admin/products.item.alcoholic_product')
                                </label>
                            </div>
                        </div>

                        <div class="column">
                            <div class="field">
                                <label class="label" for="active">@lang('admin/products.item.active')</label>
                                <label class="checkbox" for="active">
                                    <input type="checkbox" id="active" wire:model.defer="product.active">
                                    @lang('admin/products.item.active_product')
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-card-foot">
                    <button type="submit" class="button is-link">@lang('admin/products.item.edit_product')</button>
                    <button type="button" class="button" wire:click="$set('isEditing', false)" wire:loading.attr="disabled">@lang('admin/products.item.cancel')</button>
                </div>
            </form>
        </div>
    @endif

    @if ($isDeleting)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isDeleting', false)"></div>

            <div class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/products.item.delete_product')</p>
                    <button type="button" class="delete" wire:click="$set('isDeleting', false)"></button>
                </div>

                <div class="modal-card-body">
                    <p>@lang('admin/products.item.delete_description')</p>
                </div>

                <div class="modal-card-foot">
                    <button class="button is-danger" wire:click="deleteProduct()" wire:loading.attr="disabled">@lang('admin/products.item.delete_product')</button>
                    <button class="button" wire:click="$set('isDeleting', false)" wire:loading.attr="disabled">@lang('admin/products.item.cancel')</button>
                </div>
            </div>
        </div>
    @endif
</div>
