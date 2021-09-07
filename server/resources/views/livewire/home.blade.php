<div>
    @auth
        <h1 class="title">@lang('home.header_auth', ['user.firstname' => Auth::user()->firstname])</h1>
    @else
        <h1 class="title">@lang('home.header_guest')</h1>
    @endauth

    <div class="columns">
        <div class="column">
            <h2 class="title is-4">@lang('home.latest_posts')</h2>
        </div>

        <div class="column">
            <form wire:submit.prevent="$refresh">
                <div class="field has-addons">
                    <div class="control" style="width: 100%;">
                        <input class="input" type="text" id="q" wire:model.defer="q" placeholder="@lang('home.posts_query')">
                    </div>
                    <div class="control">
                        <button class="button is-link" type="submit">@lang('home.posts_search')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if ($posts->count() > 0)
        {{ $posts->links() }}

        @foreach ($posts as $post)
            <div class="box content">
                <h3 class="is-4">{{ $post->title }}</h3>
                <p><i>@lang('home.posts_written_by', ['user.name' => $post->user->name, 'post.created_at' => $post->created_at->format('Y-m-d H:i')])</i></p>
                {!! (new Parsedown())->text($post->body) !!}
            </div>
        @endforeach

        {{ $posts->links() }}
    @else
        <p><i>@lang('home.posts_empty')</i></p>
    @endif
</div>
