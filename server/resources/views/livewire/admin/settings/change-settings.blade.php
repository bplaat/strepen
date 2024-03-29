<div style="display: flex; flex-direction: column; height: 100%;">
    @if ($isChanged)
        <div class="notification is-success">
            <button class="delete" wire:click="$set('isChanged', false)"></button>
            <p>@lang('admin/settings.change_settings.success_message')</p>
        </div>
    @endif

    <form class="box" style="flex: 1;" wire:submit.prevent="changeDetails">
        <h2 class="title is-4">@lang('admin/settings.change_settings.header')</h2>

        <div class="columns">
            <div class="column">
                <div class="field">
                    <label class="label" for="currencySymbol">@lang('admin/settings.change_settings.currency_symbol')</label>
                    <div class="control">
                        <input class="input @error('currencySymbol') is-danger @enderror" type="text" id="currencySymbol"
                            wire:model.defer="currencySymbol">
                    </div>
                    @error('currencySymbol') <p class="help is-danger">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="column">
                <div class="field">
                    <label class="label" for="currencyName">@lang('admin/settings.change_settings.currency_name')</label>
                    <div class="control">
                        <input class="input @error('currencyName') is-danger @enderror" type="text" id="currencyName"
                            wire:model.defer="currencyName">
                    </div>
                    @error('currencyName') <p class="help is-danger">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="columns">
            <div class="column">
                <div class="field">
                    <label class="label" for="minUserBalance">@lang('admin/settings.change_settings.min_user_balance')</label>

                    <div class="control has-icons-left">
                        <input class="input @error('minUserBalance') is-danger @enderror" type="number" id="minUserBalance"
                            wire:model.defer="minUserBalance" required>
                        <span class="icon is-small is-left">{{ App\Models\Setting::get('currency_symbol') }}</span>
                    </div>
                    @error('minUserBalance') <p class="help is-danger">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="column">
                <div class="field">
                    <label class="label" for="maxStripeAmount">@lang('admin/settings.change_settings.max_stripe_amount')</label>
                    <div class="control">
                        <input class="input @error('maxStripeAmount') is-danger @enderror" type="number" id="maxStripeAmount"
                            wire:model.defer="maxStripeAmount" required>
                    </div>
                    @error('maxStripeAmount') <p class="help is-danger">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="columns">
            <div class="column">
                <div class="field">
                    <label class="label" for="minorAge">@lang('admin/settings.change_settings.minor_age')</label>
                    <div class="control">
                        <input class="input @error('minorAge') is-danger @enderror" type="number" id="minorAge"
                            wire:model.defer="minorAge" required>
                    </div>
                    @error('minorAge') <p class="help is-danger">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="column">
                <div class="field">
                    <label class="label" for="paginationRows">@lang('admin/settings.change_settings.pagination_rows')</label>
                    <div class="control">
                        <input class="input @error('paginationRows') is-danger @enderror" type="number" id="paginationRows"
                            wire:model.defer="paginationRows" required>
                    </div>
                    @error('paginationRows') <p class="help is-danger">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="field mb-5">
            <label class="label" for="kioskIpWhitelist">@lang('admin/settings.change_settings.kiosk_ip_whitelist')</label>
            <div class="control">
                <input class="input @error('kioskIpWhitelist') is-danger @enderror" type="text" id="kioskIpWhitelist"
                    wire:model.defer="kioskIpWhitelist">
            </div>
            @error('kioskIpWhitelist') <p class="help is-danger">{{ $message }}</p> @enderror
        </div>

        <div class="columns">
            <div class="column">
                <div class="field">
                    <label class="label" for="leaderboardsEnabled">@lang('admin/settings.change_settings.leaderboards_enabled')</label>
                    <label class="checkbox" for="leaderboardsEnabled">
                        <input type="checkbox" id="leaderboardsEnabled" wire:model.defer="leaderboardsEnabled">
                        @lang('admin/settings.change_settings.leaderboards_enabled_info')
                    </label>
                </div>
            </div>

            <div class="column">
                <div class="field">
                    <label class="label" for="casinoEnabled">@lang('admin/settings.change_settings.casino_enabled')</label>
                    <label class="checkbox" for="casinoEnabled">
                        <input type="checkbox" id="casinoEnabled" wire:model.defer="casinoEnabled">
                        @lang('admin/settings.change_settings.casino_enabled_info')
                    </label>
                </div>
            </div>
        </div>

        <div class="columns">
            <div class="column">
                <div class="field">
                    <label class="label" for="casinoSpinPrice">@lang('admin/settings.change_settings.casino_spin_price')</label>
                    <div class="control has-icons-left">
                        <input class="input @error('casinoSpinPrice') is-danger @enderror" type="number" id="casinoSpinPrice"
                            step="0.01" wire:model.defer="casinoSpinPrice" required>
                        <span class="icon is-small is-left">{{ App\Models\Setting::get('currency_symbol') }}</span>
                    </div>
                    @error('casinoSpinPrice') <p class="help is-danger">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="column">
                <div class="field">
                    <label class="label" for="casinoSpinPot">@lang('admin/settings.change_settings.casino_spin_pot')</label>
                    <div class="control has-icons-left">
                        <input class="input" type="text" id="casinoSpinPot" value="{{ App\Models\Setting::get('casino_spin_pot') }}" disabled>
                        <span class="icon is-small is-left">{{ App\Models\Setting::get('currency_symbol') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="columns">
            <div class="column">
                <div class="field">
                    <label class="label" for="bankAccountIban">@lang('admin/settings.change_settings.bank_account_iban')</label>
                    <div class="control">
                        <input class="input @error('bankAccountIban') is-danger @enderror" type="text" id="bankAccountIban"
                            wire:model.defer="bankAccountIban">
                    </div>
                    @error('bankAccountIban') <p class="help is-danger">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="column">
                <div class="field">
                    <label class="label" for="bankAccountHolder">@lang('admin/settings.change_settings.bank_account_holder')</label>
                    <div class="control">
                        <input class="input @error('bankAccountHolder') is-danger @enderror" type="text" id="bankAccountHolder"
                            wire:model.defer="bankAccountHolder">
                    </div>
                    @error('bankAccountHolder') <p class="help is-danger">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="columns">
            <div class="column">
                <div class="field">
                    <label class="label">@lang('admin/settings.change_settings.product_beer_id')</label>
                    <livewire:components.product-chooser name="product_beer" :productId="$productBeerId" inline="true" />
                    @error('productBeerId') <p class="help is-danger">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="column">
                <div class="field">
                    <label class="label">@lang('admin/settings.change_settings.product_soda_id')</label>
                    <livewire:components.product-chooser name="product_soda" :productId="$productSodaId" inline="true" />
                    @error('productSodaId') <p class="help is-danger">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="columns">
            <div class="column">
                <div class="field">
                    <label class="label">@lang('admin/settings.change_settings.product_candybar_id')</label>
                    <livewire:components.product-chooser name="product_candybar" :productId="$productCandybarId" inline="true" />
                    @error('productCandybarId') <p class="help is-danger">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="column">
                <div class="field">
                    <label class="label">@lang('admin/settings.change_settings.product_chips_id')</label>
                    <livewire:components.product-chooser name="product_chips" :productId="$productChipsId" inline="true" />
                    @error('productChipsId') <p class="help is-danger">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="field">
            <div class="control">
                <button class="button is-link" type="submit">@lang('admin/settings.change_settings.button')</button>
            </div>
        </div>
    </form>
</div>
