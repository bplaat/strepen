<div class="container content">
    <h1 class="title">@lang('notifications.header')</h1>

    @if ($notifications->count() > 0)
        {{ $notifications->links() }}

        <div class="columns is-multiline">
            @foreach ($notifications as $notification)
                <div class="column is-one-third">
                    <div class="box" style="height: 100%;">
                        @if ($notification->type == 'App\Notifications\NewDeposit')
                            @php
                                $transaction = App\Models\Transaction::find($notification->data['transaction_id']);
                            @endphp
                            <h1 class="title is-5">
                                <a href="{{ route('transactions.history') }}">@lang('notifications.new_deposit_header')</a>
                                @if ($notification->read_at == null)
                                    <span class="tag is-warning is-pulled-right is-hidden-touch">{{ Str::upper(__('notifications.unread')) }}</span>
                                @endif
                            </h1>
                            @if ($notification->read_at == null)
                                <p class="is-display-touch is-hidden-desktop">
                                    <span class="tag is-warning">{{ Str::upper(__('notifications.unread')) }}</span>
                                </p>
                            @endif
                            <p>@lang('notifications.new_deposit_start') <x-money-format :money="$transaction->price" />
                                @lang('notifications.new_deposit_end') {{ $transaction->created_at->format('Y-m-d H:i:s') }}</p>
                        @endif

                        @if ($notification->type == 'App\Notifications\NewPost')
                            @php
                                $post = App\Models\Post::find($notification->data['post_id']);
                            @endphp
                            <h1 class="title is-5">
                                <a href="{{ route('home') }}">@lang('notifications.new_post_header')</a>
                                @if ($notification->read_at == null)
                                    <span class="tag is-warning is-pulled-right is-hidden-touch">{{ Str::upper(__('notifications.unread')) }}</span>
                                @endif
                            </h1>
                            @if ($notification->read_at == null)
                                <p class="is-display-touch is-hidden-desktop">
                                    <span class="tag is-warning">{{ Str::upper(__('notifications.unread')) }}</span>
                                </p>
                            @endif
                            <p>@lang('notifications.new_post_text', ['post.created_at' => $post->created_at->format('Y-m-d H:i:s')])</p>
                        @endif

                        @if ($notification->type == 'App\Notifications\LowBalance')
                            <h1 class="title is-5">
                                <a href="{{ route('balance') }}">@lang('notifications.low_balance_header')</a>
                                @if ($notification->read_at == null)
                                    <span class="tag is-warning is-pulled-right is-hidden-touch">{{ Str::upper(__('notifications.unread')) }}</span>
                                @endif
                            </h1>
                            @if ($notification->read_at == null)
                                <p class="is-display-touch is-hidden-desktop">
                                    <span class="tag is-warning">{{ Str::upper(__('notifications.unread')) }}</span>
                                </p>
                            @endif
                            <p>@lang('notifications.low_balance_start') <x-money-format :money="$notification->data['balance']" />
                                @lang('notifications.low_balance_end')</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{ $notifications->links() }}
    @else
        <p><i>@lang('notifications.empty')</i></p>
    @endif
</div>
