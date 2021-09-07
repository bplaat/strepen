<div>
    <h2 class="title is-4">@lang('admin/posts.crud.header')</h2>

    <div class="columns">
        <div class="column">
            <div class="buttons">
                <button class="button is-link" wire:click="$set('isCreating', true)" wire:loading.attr="disabled">@lang('admin/posts.crud.create_post')</button>
            </div>
        </div>

        <div class="column">
            <form wire:submit.prevent="$refresh">
                <div class="field has-addons">
                    <div class="control" style="width: 100%;">
                        <input class="input" type="text" id="q" wire:model.defer="q" placeholder="@lang('admin/posts.crud.query')">
                    </div>
                    <div class="control">
                        <button class="button is-link" type="submit">@lang('admin/posts.crud.search')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if ($posts->count() > 0)
        {{ $posts->links() }}

        <div class="columns is-multiline">
            @foreach ($posts as $post)
                @livewire('admin.posts.item', ['post' => $post], key($post->id))
            @endforeach
        </div>

        {{ $posts->links() }}
    @else
        <p><i>@lang('admin/posts.crud.empty')</i></p>
    @endif

    @if ($isCreating)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isCreating', false)"></div>

            <form wire:submit.prevent="createPost" class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/posts.crud.create_post')</p>
                    <button type="button" class="delete" wire:click="$set('isCreating', false)"></button>
                </header>

                <section class="modal-card-body">
                    <div class="field">
                        <label class="label" for="title">@lang('admin/posts.crud._title')</label>
                        <div class="control">
                            <input class="input @error('post.title') is-danger @enderror" type="text" id="title"
                                wire:model.defer="post.title" tabindex="1" required>
                        </div>
                        @error('post.title') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label class="label" for="body">@lang('admin/posts.crud.body', ['markdown_link' => '<a href="https://en.wikipedia.org/wiki/Markdown#Example" target="_blank" tabindex="3">Markdown</a>'])</label>
                        <div class="control">
                            <textarea class="textarea is-family-monospace has-fixed-size @error('post.body') is-danger @enderror" id="body"
                                wire:model.defer="post.body" rows="12" tabindex="2" required></textarea>
                        </div>
                        @error('post.body') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>
                </section>

                <footer class="modal-card-foot">
                    <button type="submit" class="button is-link">@lang('admin/posts.crud.create_post')</button>
                    <button type="button" class="button" wire:click="$set('isCreating', false)" wire:loading.attr="disabled">@lang('admin/posts.crud.cancel')</button>
                </footer>
            </form>
        </div>
    @endif
</div>
