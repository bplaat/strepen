<div class="column is-one-third">
    <div class="card">
        <div class="card-content content">
            <h4>{{ $post->title }}</h4>
            <p><i>@lang('admin/posts.item.written_by', ['user.name' => $post->user != null ? $post->user->name : '?', 'post.created_at' => $post->created_at->format('Y-m-d H:i')])</i></p>
            <pre>{{  Str::limit(str_replace(["\r", "\n"], '', $post->body), 240) }}</pre>
        </div>

        <div class="card-footer">
            <a href="#" class="card-footer-item" wire:click.prevent="$set('isEditing', true)">@lang('admin/posts.item.edit')</a>
            <a href="#" class="card-footer-item has-text-danger" wire:click.prevent="$set('isDeleting', true)">@lang('admin/posts.item.delete')</a>
        </div>
    </div>

    @if ($isEditing)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isEditing', false)"></div>

            <form wire:submit.prevent="editPost" class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/posts.item.edit_post')</p>
                    <button type="button" class="delete" wire:click="$set('isEditing', false)"></button>
                </div>

                <div class="modal-card-body">
                    <livewire:components.user-chooser :userId="$post->user_id" includeStrepenUser="true" validate="true" />

                    <div class="field">
                        <label class="label" for="title">@lang('admin/posts.item.title')</label>
                        <div class="control">
                            <input class="input @error('post.title') is-danger @enderror" type="text" id="title"
                                wire:model.defer="post.title" tabindex="2" required>
                        </div>
                        @error('post.title') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="columns">
                        <div class="column">
                            <div class="field">
                                <label class="label" for="created_at_date">@lang('admin/posts.item.created_at_date')</label>
                                <div class="control">
                                    <input class="input @error('createdAtDate') is-danger @enderror" type="date" id="created_at_date"
                                        wire:model.defer="createdAtDate" tabindex="3" required>
                                </div>
                                @error('createdAtDate') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="column">
                            <div class="field">
                                <label class="label" for="created_at_time">@lang('admin/posts.item.created_at_time')</label>
                                <div class="control">
                                    <input class="input @error('createdAtTime') is-danger @enderror" type="time" step="1" id="created_at_time"
                                        wire:model.defer="createdAtTime" tabindex="4" required>
                                </div>
                                @error('createdAtTime') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label" for="body">@lang('admin/posts.item.body', ['markdown_link' => '<a href="https://en.wikipedia.org/wiki/Markdown#Example" target="_blank" tabindex="6">Markdown</a>'])</label>
                        <div class="control">
                            <textarea class="textarea is-family-monospace has-fixed-size @error('post.body') is-danger @enderror" id="postBody"
                                wire:model.defer="post.body" rows="12" tabindex="5" required></textarea>
                        </div>
                        @error('post.body') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="modal-card-foot">
                    <button type="submit" class="button is-link">@lang('admin/posts.item.edit_post')</button>
                    <button type="button" class="button" wire:click="$set('isEditing', false)" wire:loading.attr="disabled">@lang('admin/posts.item.cancel')</button>
                </div>
            </form>
        </div>
    @endif

    @if ($isDeleting)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isDeleting', false)"></div>

            <div class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/posts.item.delete_post')</p>
                    <button type="button" class="delete" wire:click="$set('isDeleting', false)"></button>
                </div>

                <div class="modal-card-body">
                    <p>@lang('admin/posts.item.delete_description')</p>
                </div>

                <div class="modal-card-foot">
                    <button class="button is-danger" wire:click="deletePost()" wire:loading.attr="disabled">@lang('admin/posts.item.delete_post')</button>
                    <button class="button" wire:click="$set('isDeleting', false)" wire:loading.attr="disabled">@lang('admin/posts.item.cancel')</button>
                </div>
            </div>
        </div>
    @endif
</div>
