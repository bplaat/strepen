<div style="margin: 1.5em 0;">
    @if (session()->has('change_details_message'))
        <div class="notification is-success">
            <button class="delete" onclick="this.parentNode.parentNode.removeChild(this.parentNode);"></button>
            <p>{{ session('change_details_message') }}</p>
        </div>
    @endif

    <form class="box" wire:submit.prevent="changeDetails">
        <h2 class="title is-4">@lang('settings.change_details.header')</h2>

        <div class="columns">
            <div class="column">
                <div class="field">
                    <label class="label" for="firstname">@lang('settings.change_details.firstname')</label>
                    <div class="control">
                        <input class="input @error('user.firstname') is-danger @enderror" type="text" id="firstname"
                            wire:model.defer="user.firstname" required>
                    </div>
                    @error('user.firstname') <p class="help is-danger">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="column">
                <div class="field">
                    <label class="label" for="insertion">@lang('settings.change_details.insertion')</label>
                    <div class="control">
                        <input class="input @error('user.insertion') is-danger @enderror" type="text" id="insertion"
                            wire:model.defer="user.insertion">
                    </div>
                    @error('user.insertion') <p class="help is-danger">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="column">
                <div class="field">
                    <label class="label" for="lastname">@lang('settings.change_details.lastname')</label>
                    <div class="control">
                        <input class="input @error('user.lastname') is-danger @enderror" type="text" id="lastname"
                            wire:model.defer="user.lastname" required>
                    </div>
                    @error('user.lastname') <p class="help is-danger">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="columns">
            <div class="column">
                <div class="field">
                    <label class="label" for="gender">@lang('settings.change_details.gender')</label>
                    <div class="control">
                        <div class="select is-fullwidth @error('user.gender') is-danger @enderror">
                            <select id="gender" wire:model.defer="user.gender">
                                <option value="">@lang('settings.change_details.gender_null')</option>
                                <option value="{{ App\Models\User::GENDER_MALE }}">@lang('settings.change_details.gender_male')</option>
                                <option value="{{ App\Models\User::GENDER_FEMALE }}">@lang('settings.change_details.gender_female')</option>
                                <option value="{{ App\Models\User::GENDER_OTHER }}">@lang('settings.change_details.gender_other')</option>
                            </select>
                        </div>
                    </div>
                    @error('user.gender') <p class="help is-danger">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="column">
                <div class="field">
                    <label class="label" for="birthday">@lang('settings.change_details.birthday')</label>
                    <div class="control">
                        <input class="input @error('user.birthday') is-danger @enderror" type="date" id="birthday" wire:model.defer="user.birthday">
                    </div>
                    @error('user.birthday') <p class="help is-danger">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="columns">
            <div class="column">
                <div class="field">
                    <label class="label" for="email">@lang('settings.change_details.email')</label>
                    <div class="control">
                        <input class="input @error('user.email') is-danger @enderror" type="email" id="email" wire:model.defer="user.email" required>
                    </div>
                    @error('user.email') <p class="help is-danger">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="column">
                <div class="field">
                    <label class="label" for="phone">@lang('settings.change_details.phone')</label>
                    <div class="control">
                        <input class="input @error('userPhone') is-danger @enderror" type="tel" id="phone" wire:model.defer="user.phone">
                    </div>
                    @error('user.phone') <p class="help is-danger">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="columns">
            <div class="column">
                <div class="field">
                    <label class="label" for="address">@lang('settings.change_details.address')</label>
                    <div class="control">
                        <input class="input @error('user.address') is-danger @enderror" type="text" id="address" wire:model.defer="user.address">
                    </div>
                    @error('user.address') <p class="help is-danger">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="column">
                <div class="field">
                    <label class="label" for="postcode">@lang('settings.change_details.postcode')</label>
                    <div class="control">
                        <input class="input @error('user.postcode') is-danger @enderror" type="text" id="postcode" wire:model.defer="user.postcode">
                    </div>
                    @error('user.postcode')
                        <p class="help is-danger">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="column">
                <div class="field">
                    <label class="label" for="city">@lang('settings.change_details.city')</label>
                    <div class="control">
                        <input class="input @error('user.city') is-danger @enderror" type="text" id="city" wire:model.defer="user.city">
                    </div>
                    @error('user.city') <p class="help is-danger">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="columns">
            <div class="column">
                <div class="field">
                    <label class="label" for="language">@lang('settings.change_details.language')</label>
                    <div class="control">
                        <div class="select is-fullwidth @error('user.language') is-danger @enderror">
                            <select id="language" wire:model.defer="user.language">
                                <option value="{{ App\Models\User::LANGUAGE_ENGLISH }}">English</option>
                                <option value="{{ App\Models\User::LANGUAGE_DUTCH }}">Nederlands</option>
                            </select>
                        </div>
                    </div>
                    @error('user.language') <p class="help is-danger">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="column">
                <div class="field">
                    <label class="label" for="theme">@lang('admin/users.crud.theme')</label>
                    <div class="control">
                        <div class="select is-fullwidth @error('user.theme') is-danger @enderror">
                            <select id="theme" wire:model.defer="user.theme">
                                <option value="{{ App\Models\User::THEME_LIGHT }}">@lang('admin/users.crud.theme_light')</option>
                                <option value="{{ App\Models\User::THEME_DARK }}">@lang('admin/users.crud.theme_dark')</option>
                            </select>
                        </div>
                    </div>
                    @error('user.theme') <p class="help is-danger">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="field">
            <div class="control">
                <button class="button is-link" type="submit">@lang('settings.change_details.button')</button>
            </div>
        </div>
    </form>
</div>
