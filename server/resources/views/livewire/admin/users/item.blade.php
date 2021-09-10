<div class="column is-one-third">
    <div class="card" style="display: flex; flex-direction: column; height: 100%; margin-bottom: 0; overflow: hidden;">
        <div class="card-content content" style="flex: 1; margin-bottom: 0;">
            <h4>
                {{ $user->name }}

                @if ($user->role == App\Models\User::ROLE_NORMAL)
                    <span class="tag is-pulled-right is-success">{{ Str::upper(__('admin/users.item.role_normal')) }}</span>
                @endif

                @if ($user->role == App\Models\User::ROLE_ADMIN)
                    <span class="tag is-pulled-right is-danger">{{ Str::upper(__('admin/users.item.role_admin')) }}</span>
                @endif
            </h4>

            <p>@lang('admin/users.item.balance'): <strong @if ($user->balance < 0) class="has-text-danger" @endif>&euro; {{ number_format($user->balance, 2, ',', '.') }}</strong></p>
        </div>

        <div class="card-footer">
            @if ($user->id != Auth::id())
                <a href="#" class="card-footer-item has-text-black" wire:click.prevent="hijackUser">@lang('admin/users.item.hijack')</a>
            @endif
            <a href="#" class="card-footer-item" wire:click.prevent="$set('isEditing', true)">@lang('admin/users.item.edit')</a>
            <a href="#" class="card-footer-item has-text-danger" wire:click.prevent="$set('isDeleting', true)">@lang('admin/users.item.delete')</a>
        </div>
    </div>

    @if ($isEditing)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isEditing', false)"></div>

            <form wire:submit.prevent="editUser" class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/users.item.edit_user')</p>
                    <button type="button" class="delete" wire:click="$set('isEditing', false)"></button>
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
                                <label class="label" for="password">@lang('admin/users.item.password')</label>
                                <div class="control">
                                    <input class="input @error('newPassword') is-danger @enderror" type="password" id="password" wire:model.defer="newPassword">
                                </div>
                                @error('newPassword')
                                    <p class="help is-danger">{{ $message }}</p>
                                @else
                                    <p class="help">@lang('admin/users.item.password_hint')</p>
                                @enderror
                            </div>
                        </div>

                        <div class="column">
                            <div class="field">
                                <label class="label" for="password_confirmation">@lang('admin/users.item.password_confirmation')</label>
                                <div class="control">
                                    <input class="input @error('newPasswordConfirmation') is-danger @enderror" type="password" id="password_confirmation" wire:model.defer="newPasswordConfirmation">
                                </div>
                                @error('newPasswordConfirmation') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label" for="avatar">@lang('admin/users.item.avatar')</label>
                        @if ($user->avatar != null)
                            <div class="box" style="background-color: #ccc; width: 50%;">
                                <div style="background-image: url(/storage/avatars/{{ $user->avatar }}); background-size: cover; background-position: center center; padding-top: 100%;"></div>
                            </div>
                        @endif
                    </div>

                    <div class="columns">
                        <div class="column">
                            <div class="field">
                                <div class="control">
                                    <input class="input @error('avatar') is-danger @enderror" type="file" accept=".jpg,.jpeg,.png"
                                        id="avatar" wire:model="avatar">
                                </div>
                                @error('avatar')
                                    <p class="help is-danger">{{ $message }}</p>
                                @else
                                    <p class="help">@lang('admin/users.item.avatar_help')</p>
                                @enderror
                            </div>
                        </div>

                        @if ($user->avatar != null)
                            <div class="column">
                                <div class="field">
                                    <div class="control">
                                        <button type="button" class="button is-danger" wire:click="deleteAvatar" wire:loading.attr="disabled">@lang('admin/users.item.delete_avatar')</button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="field">
                        <label class="label" for="role">@lang('admin/users.item.role')</label>
                        <div class="control">
                            <div class="select is-fullwidth @error('user.role') is-danger @enderror">
                                <select id="role" wire:model.defer="user.role">
                                    <option value="{{ App\Models\User::ROLE_NORMAL }}">@lang('admin/users.item.role_normal')</option>
                                    <option value="{{ App\Models\User::ROLE_ADMIN }}">@lang('admin/users.item.role_admin')</option>
                                </select>
                            </div>
                        </div>
                        @error('user.role') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>
                </section>

                <footer class="modal-card-foot">
                    <button type="submit" class="button is-link">@lang('admin/users.item.edit_user')</button>
                    <button type="button" class="button" wire:click="$set('isEditing', false)">@lang('admin/users.item.cancel')</button>
                </footer>
            </form>
        </div>
    @endif

    @if ($isDeleting)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isDeleting', false)"></div>

            <div class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/users.item.delete_user')</p>
                    <button type="button" class="delete" wire:click="$set('isDeleting', false)"></button>
                </header>

                <section class="modal-card-body">
                    <p>@lang('admin/users.item.delete_description')</p>
                </section>

                <footer class="modal-card-foot">
                    <button class="button is-danger" wire:click="deleteUser()" wire:loading.attr="disabled">@lang('admin/users.item.delete_user')</button>
                    <button class="button" wire:click="$set('isDeleting', false)" wire:loading.attr="disabled">@lang('admin/users.item.cancel')</button>
                </footer>
            </div>
        </div>
    @endif
</div>
