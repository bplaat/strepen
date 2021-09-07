@component('layouts.app')
    @slot('title', __('home.title'))

    @auth
        <h1 class="title">@lang('home.header_auth', ['user.firstname' => Auth::user()->firstname])</h1>
    @else
        <h1 class="title">@lang('home.header_guest')</h1>
    @endauth

    <h2 class="title is-4">Latest posts</h2>

    @foreach ($latestPosts as $post)
        <div class="box content">
            <h3 class="is-4">{{ $post->title }}</h3>
            <p><i>Written by {{ $post->user->name }} on {{ $post->created_at->format('Y-m-d H:i') }}</i></p>
            {!! (new Parsedown())->text($post->body) !!}
        </div>
    @endforeach
@endcomponent
