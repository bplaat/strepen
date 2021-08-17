<div class="column is-one-third">
    <div class="box content" style="height: 100%;">
        <h3 class="is-3">{{ $post->title }}</h3>
        <p><i>Written by {{ $post->user->name }} on {{ $post->created_at->format('Y-m-d H:i') }}</i></p>
        <pre>{{  Str::limit($post->body, 320) }}</pre>

        <div class="buttons">
            <button type="button" class="button is-link" wire:click="$set('isEditing', true)">Edit</button>
            <button type="button" class="button is-danger" wire:click="$set('isDeleting', true)">Delete</button>
        </div>
    </div>

    @if ($isEditing)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isEditing', false)"></div>

            <form wire:submit.prevent="updatePost" class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title">Update post</p>
                    <button type="button" class="delete" wire:click="$set('isEditing', false)"></button>
                </header>

                <section class="modal-card-body">
                    <div class="field">
                        <label class="label" for="postTitle">Title</label>
                        <div class="control">
                            <input class="input @error('post.title') is-danger @enderror" type="text" id="postTitle"
                                wire:model.defer="post.title" required>
                        </div>
                        @error('post.title') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label class="label" for="postBody">Body (support <a href="https://en.wikipedia.org/wiki/Markdown#Example" target="_blank">Markdown</a>)</label>
                        <div class="control">
                            <textarea class="textarea has-fixed-size @error('post.body') is-danger @enderror" id="postBody"
                                wire:model.defer="post.body" rows="12" required></textarea>
                        </div>
                        @error('post.body') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>
                </section>

                <footer class="modal-card-foot">
                    <button type="submit" class="button is-link">Update post</button>
                    <button type="button" class="button" wire:click="$set('isEditing', false)">Cancel</button>
                </footer>
            </form>
        </div>
    @endif

    @if ($isDeleting)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isDeleting', false)"></div>

            <div class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title">Delete post confirmation</p>
                    <button type="button" class="delete" wire:click="$set('isDeleting', false)"></button>
                </header>

                <section class="modal-card-body">
                    <p>Are you sure you want to delete this post?</p>
                </section>

                <footer class="modal-card-foot">
                    <button class="button is-danger" wire:click="deletePost()">Delete post</button>
                    <button class="button" wire:click="$set('isDeleting', false)">Cancel</button>
                </footer>
            </div>
        </div>
    @endif
</div>
