<div class="container">
    <h2 class="title is-4">Posts</h2>

    <div class="columns">
        <div class="column">
            <div class="buttons">
                <button class="button is-link" wire:click="$set('isCreating', true)">Create new post</button>
            </div>
        </div>

        <div class="column">
            <form wire:submit.prevent="searchPost">
                <div class="field has-addons">
                    <div class="control" style="width: 100%;">
                        <input class="input" type="text" id="q" wire:model.defer="q" placeholder="Search for posts...">
                    </div>
                    <div class="control">
                        <button class="button is-link" type="submit">Search</button>
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
        <p><i>No posts found!</i></p>
    @endif

    @if ($isCreating)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isCreating', false)"></div>

            <form wire:submit.prevent="createPost" class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title">Create new post</p>
                    <button type="button" class="delete" wire:click="$set('isCreating', false)"></button>
                </header>

                <section class="modal-card-body">
                    <div class="field">
                        <label class="label" for="postTitle">Title</label>
                        <div class="control">
                            <input class="input @error('postTitle') is-danger @enderror" type="text" id="postTitle"
                                wire:model.defer="postTitle" required>
                        </div>
                        @error('postTitle') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label class="label" for="postBody">Body (support <a href="https://en.wikipedia.org/wiki/Markdown#Example" target="_blank">Markdown</a>)</label>
                        <div class="control">
                            <textarea class="textarea has-fixed-size @error('postBody') is-danger @enderror" id="postBody"
                                wire:model.defer="postBody" rows="12" required></textarea>
                        </div>
                        @error('postBody') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>
                </section>

                <footer class="modal-card-foot">
                    <button type="submit" class="button is-link">Create new post</button>
                    <button type="button" class="button" wire:click="$set('isCreating', false)">Cancel</button>
                </footer>
            </form>
        </div>
    @endif
</div>
