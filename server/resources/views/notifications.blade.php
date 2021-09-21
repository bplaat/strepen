@component('layouts.app')
    @slot('title', __('notifications.title'))

    <div class="container content">
        <h1 class="title">@lang('notifications.header')</h1>
        @php
            $notifications = Auth::user()->notifications->paginate(config('pagination.web.limit'));
        @endphp
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
                                <h1 class="title is-6" style="margin-bottom: 4px;">
                                    @lang('notifications.new_deposit_header')

                                    @if ($notification->read_at == null)
                                        <span class="tag is-warning is-pulled-right">{{ Str::upper(__('notifications.unread')) }}</span>
                                    @endif
                                </h1>
                                <p>@lang('notifications.new_deposit_text') @component('components.money-format', ['money' => $transaction->price])@endcomponent
                                    @lang('notifications.new_deposit_on') {{ $transaction->created_at->format('Y-m-d H:i:s') }}</p>
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
@endcomponent
