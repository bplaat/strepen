<div class="container is-max-desktop">
    <livewire:posts.item :post="$post" standalone="true" />

    <div class="buttons mt-5 is-centered">
        <a class="button is-link" href="{{ route('home') }}">@lang('posts.show.go_back')</a>
    </div>
</div>
