<div class="column is-one-third">
    <div class="box content" style="height: 100%;">
        <h3 class="is-3">{{ $post->title }}</h3>
        <p><i>@lang('admin/posts.item.written_by', ['user.name' => $post->user->name, 'post.created_at' => $post->created_at->format('Y-m-d H:i')])</i></p>
        <pre>{{  Str::limit($post->body, 320) }}</pre>

        <div class="buttons">
            <button type="button" class="button is-link" wire:click="$set('isEditing', true)">@lang('admin/posts.item.edit')</button>
            <button type="button" class="button is-danger" wire:click="$set('isDeleting', true)">@lang('admin/posts.item.delete')</button>
        </div>
    </div>

    @if ($isEditing)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isEditing', false)"></div>

            <form wire:submit.prevent="editPost" class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/posts.item.edit_post')</p>
                    <button type="button" class="delete" wire:click="$set('isEditing', false)"></button>
                </header>

                <section class="modal-card-body">
                    <div class="field">
                        <label class="label" for="title">@lang('admin/posts.item._title')</label>
                        <div class="control">
                            <input class="input @error('post.title') is-danger @enderror" type="text" id="title"
                                wire:model.defer="post.title" tabindex="1" required>
                        </div>
                        @error('post.title') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label class="label" for="body">@lang('admin/posts.item.body', ['markdown_link' => '<a href="https://en.wikipedia.org/wiki/Markdown#Example" target="_blank" tabindex="3">Markdown</a>'])</label>
                        <div class="control">
                            <textarea class="textarea has-fixed-size @error('post.body') is-danger @enderror" id="body"
                                wire:model.defer="post.body" rows="12" tabindex="2" required></textarea>
                        </div>
                        @error('post.body') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>
                </section>

                <footer class="modal-card-foot">
                    <button type="submit" class="button is-link">@lang('admin/posts.item.edit_post')</button>
                    <button type="button" class="button" wire:click="$set('isEditing', false)">@lang('admin/posts.item.cancel')</button>
                </footer>
            </form>
        </div>
    @endif

    @if ($isDeleting)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isDeleting', false)"></div>

            <div class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/posts.item.delete_post')</p>
                    <button type="button" class="delete" wire:click="$set('isDeleting', false)"></button>
                </header>

                <section class="modal-card-body">
                    <p>@lang('admin/posts.item.delete_description')</p>
                </section>

                <footer class="modal-card-foot">
                    <button class="button is-danger" wire:click="deletePost()">@lang('admin/posts.item.delete_post')</button>
                    <button class="button" wire:click="$set('isDeleting', false)">@lang('admin/posts.item.cancel')</button>
                </footer>
            </div>
        </div>
    @endif
</div>
