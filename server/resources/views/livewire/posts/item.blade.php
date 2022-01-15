<div class="card @if (!$standalone) my-5 @endif" style="overflow: hidden;">
    @if ($post->image != null)
        <div class="card-image">
            <div class="image is-widescreen" style="background-image: url(/storage/posts/{{ $post->image }});"></div>
        </div>
    @endif

    <div class="card-content content">
        <h4><a href="{{ route('posts.show', $post) }}" style="color: inherit;">{{ $post->title }}</a></h4>
        <p><i>@lang('posts.item.posts_written_by', ['user.name' => $post->user->name, 'post.created_at' => $post->created_at->format('Y-m-d H:i')])</i></p>
        {!! (new Parsedown())->text($post->body) !!}
    </div>
</div>
