<div>
    <h2 class="title is-4">@lang('admin/users.crud.header')</h2>

    <div class="columns">
        <div class="column">
            <div class="buttons">
                <button class="button is-link" wire:click="$set('isCreating', true)" wire:loading.attr="disabled">@lang('admin/users.crud.create_user')</button>
            </div>
        </div>

        <div class="column">
            <form wire:submit.prevent="$refresh">
                <div class="field has-addons">
                    <div class="control" style="width: 100%;">
                        <input class="input" type="text" id="q" wire:model.defer="q" placeholder="@lang('admin/users.crud.query')">
                    </div>
                    <div class="control">
                        <button class="button is-link" type="submit">@lang('admin/users.crud.search')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if ($users->count() > 0)
        {{ $users->links() }}

        <div class="columns is-multiline">
            @foreach ($users as $user)
                @livewire('admin.users.item', ['user' => $user], key($user->id))
            @endforeach
        </div>

        {{ $users->links() }}
    @else
        <p><i>@lang('admin/users.crud.empty')</i></p>
    @endif

    @if ($isCreating)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isCreating', false)"></div>

            <form wire:submit.prevent="createUser" class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/users.crud.create_user')</p>
                    <button type="button" class="delete" wire:click="$set('isCreating', false)"></button>
                </header>

                <section class="modal-card-body">
                <div class="columns">
                        <div class="column">
                            <div class="field">
                                <label class="label" for="firstname">@lang('admin/users.item.firstname')</label>
                                <div class="control">
                                    <input class="input @error('user.firstname') is-danger @enderror" type="text" id="firstname"
                                        wire:model.defer="user.firstname" required>
                                </div>
                                @error('user.firstname') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="column">
                            <div class="field">
                                <label class="label" for="insertion">@lang('admin/users.item.insertion')</label>
                                <div class="control">
                                    <input class="input @error('user.insertion') is-danger @enderror" type="text" id="insertion"
                                        wire:model.defer="user.insertion">
                                </div>
                                @error('user.insertion') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="column">
                            <div class="field">
                                <label class="label" for="lastname">@lang('admin/users.item.lastname')</label>
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
                                <label class="label" for="gender">@lang('admin/users.item.gender')</label>
                                <div class="control">
                                    <div class="select is-fullwidth @error('user.gender') is-danger @enderror">
                                        <select id="gender" wire:model.defer="user.gender">
                                            <option value="">@lang('admin/users.item.gender_null')</option>
                                            <option value="{{ App\Models\User::GENDER_MALE }}">@lang('admin/users.item.gender_male')</option>
                                            <option value="{{ App\Models\User::GENDER_FEMALE }}">@lang('admin/users.item.gender_female')</option>
                                            <option value="{{ App\Models\User::GENDER_OTHER }}">@lang('admin/users.item.gender_other')</option>
                                        </select>
                                    </div>
                                </div>
                                @error('user.gender') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="column">
                            <div class="field">
                                <label class="label" for="birthday">@lang('admin/users.item.birthday')</label>
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
                                <label class="label" for="email">@lang('admin/users.item.email')</label>
                                <div class="control">
                                    <input class="input @error('user.email') is-danger @enderror" type="email" id="email" wire:model.defer="user.email" required>
                                </div>
                                @error('user.email') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="column">
                            <div class="field">
                                <label class="label" for="phone">@lang('admin/users.item.phone')</label>
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
                                <label class="label" for="address">@lang('admin/users.item.address')</label>
                                <div class="control">
                                    <input class="input @error('user.address') is-danger @enderror" type="text" id="address" wire:model.defer="user.address">
                                </div>
                                @error('user.address') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="column">
                            <div class="field">
                                <label class="label" for="postcode">@lang('admin/users.item.postcode')</label>
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
                                <label class="label" for="city">@lang('admin/users.item.city')</label>
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
                                <label class="label" for="password">@lang('admin/users.crud.password')</label>
                                <div class="control">
                                    <input class="input @error('user.password') is-danger @enderror" type="password" id="password"
                                        wire:model.defer="user.password" required>
                                </div>
                                @error('user.password')
                                    <p class="help is-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="column">
                            <div class="field">
                                <label class="label" for="password_confirmation">@lang('admin/users.crud.password_confirmation')</label>
                                <div class="control">
                                    <input class="input @error('user.password_confirmation') is-danger @enderror" type="password" id="password_confirmation"
                                        wire:model.defer="user.password_confirmation" required>
                                </div>
                                @error('user.password_confirmation') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label" for="avatar">@lang('admin/users.crud.avatar')</label>
                        <div class="control">
                            <input class="input @error('userAvatar') is-danger @enderror" type="file" accept=".jpg,.jpeg,.png"
                                id="avatar" wire:model="userAvatar">
                        </div>
                        @error('userAvatar')
                            <p class="help is-danger">{{ $message }}</p>
                        @else
                            <p class="help">@lang('admin/users.crud.avatar_help')</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label class="label" for="role">@lang('admin/users.crud.role')</label>
                        <div class="control">
                            <div class="select is-fullwidth @error('user.role') is-danger @enderror">
                                <select id="role" wire:model.defer="user.role">
                                    <option value="{{ App\Models\User::ROLE_NORMAL }}">@lang('admin/users.crud.role_normal')</option>
                                    <option value="{{ App\Models\User::ROLE_ADMIN }}">@lang('admin/users.crud.role_admin')</option>
                                </select>
                            </div>
                        </div>
                        @error('user.role') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>
                </section>

                <footer class="modal-card-foot">
                    <button type="submit" class="button is-link">@lang('admin/users.crud.create_user')</button>
                    <button type="button" class="button" wire:click="$set('isCreating', false)" wire:loading.attr="disabled">@lang('admin/users.crud.cancel')</button>
                </footer>
            </form>
        </div>
    @endif
</div>