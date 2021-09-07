@if ($paginator->hasPages())
    <nav class="pagination is-centered" role="navigation" aria-label="pagination">
        <a wire:click="_previousPage({{ !$paginator->onFirstPage() ? 'false' : 'true' }})" wire:loading.attr="disabled"
            rel="previous" class="pagination-previous" @if ($paginator->onFirstPage()) disabled @endif>@lang('pagination.previous')</a>
        <a wire:click="_nextPage({{ $paginator->hasMorePages() ? 'false' : 'true' }})" wire:loading.attr="disabled"
            rel="next" class="pagination-next" @if (!$paginator->hasMorePages()) disabled @endif>@lang('pagination.next')</a>

        <ul class="pagination-list">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="pagination-ellipsis">&hellip;</span>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li>
                            <a class="pagination-link @if ($page == $paginator->currentPage()) is-current @endif"
                                wire:click="gotoPage({{ $page }})"
                                wire:loading.attr="disabled"
                                aria-label="Goto page {{ $page }}">{{ $page }}</a>
                        </li>
                    @endforeach
                @endif
            @endforeach
        </ul>
    </nav>
@endif
