<div class="container">
    @auth
        <h1 class="title">@lang('home.header_auth', ['user.firstname' => Auth::user()->firstname])</h1>
    @else
        <h1 class="title">@lang('home.header_guest')</h1>
    @endauth

    <x-search-header :itemName="__('home.posts')">
        <h2 class="title is-4">@lang('home.latest_posts')</h2>

        <x-slot name="fields">
            <livewire:components.user-chooser :userId="$user_id" inline="true" includeStrepenUser="true" relationship="true" postsRequired="true" />
        </x-slot>
    </x-search-header>

    @if ($posts->count() > 0)
        {{ $posts->links() }}

        @php
            $parsedown = new Parsedown();
        @endphp

        @foreach ($posts as $post)
            <div class="box content">
                <h4>{{ $post->title }}</h4>
                <p><i>@lang('home.posts_written_by', ['user.name' => $post->user->name, 'post.created_at' => $post->created_at->format('Y-m-d H:i')])</i></p>
                {!! $parsedown->text($post->body) !!}
            </div>
        @endforeach

        {{ $posts->links() }}
    @else
        <p><i>@lang('home.posts_empty')</i></p>
    @endif
</div>
