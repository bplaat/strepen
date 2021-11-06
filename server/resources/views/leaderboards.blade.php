@component('layouts.app')
    @slot('title', __('leaderboards.title'))

    <div class="container content">
        <h1 class="title">@lang('leaderboards.header')</h1>

        <div class="columns is-multiline">
            <div class="column is-half">
                <div class="box">
                    <h2 class="title is-4 has-text-centered">@lang('leaderboards.best_beer_header')</h2>

                    @php
                        $beerProductId = 1; // Weird constant
                        $beerUsers = App\Models\User::where('deleted', false)->where('active', true)->get()
                            ->map(function ($user) use ($beerProductId) { // Very slow
                                $user->amount = DB::table('transaction_product')
                                    ->join('transactions', 'transactions.id', 'transaction_id')
                                    ->where('deleted', false)
                                    ->where('user_id', $user->id)
                                    ->where('product_id', $beerProductId)
                                    ->sum('amount');
                                return $user;
                            })
                            ->sortByDesc('amount')->values()
                            ->slice(0, 10);
                    @endphp

                    <table class="table is-fullwidth">
                        <thead>
                            <tr>
                                <th class="medal-column">#</th>
                                <th>@lang('leaderboards.name')</th>
                                <th style="width: 30%;">@lang('leaderboards.amount')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($beerUsers as $index => $user)
                                <tr>
                                    <td><x-index-medal :index="$index" /></td>
                                    <td style="vertical-align: middle;">
                                        <div class="image is-small is-round is-inline" style="background-image: url(/storage/avatars/{{ $user->avatar != null ? $user->avatar : App\Models\Setting::get('default_user_avatar') }});"></div>
                                        {{ $user->name }}
                                    </td>
                                    <td><x-amount-format :amount="$user->amount" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="column is-half">
                <div class="box">
                    <h2 class="title is-4 has-text-centered">@lang('leaderboards.best_soda_header')</h2>

                    @php
                        $sodaProductId = 2; // Weird constant
                        $sodaUsers = App\Models\User::where('deleted', false)->where('active', true)->get()
                            ->map(function ($user) use ($sodaProductId) { // Very slow
                                $user->amount = DB::table('transaction_product')
                                    ->join('transactions', 'transactions.id', 'transaction_id')
                                    ->where('deleted', false)
                                    ->where('user_id', $user->id)
                                    ->where('product_id', $sodaProductId)
                                    ->sum('amount');
                                return $user;
                            })
                            ->sortByDesc('amount')->values()
                            ->slice(0, 10);
                    @endphp

                    <table class="table is-fullwidth">
                        <thead>
                            <tr>
                                <th class="medal-column">#</th>
                                <th>@lang('leaderboards.name')</th>
                                <th style="width: 30%;">@lang('leaderboards.amount')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sodaUsers as $index => $user)
                                <tr>
                                    <td><x-index-medal :index="$index" /></td>
                                    <td style="vertical-align: middle;">
                                        <div class="image is-small is-round is-inline" style="background-image: url(/storage/avatars/{{ $user->avatar != null ? $user->avatar : App\Models\Setting::get('default_user_avatar') }});"></div>
                                        {{ $user->name }}
                                    </td>
                                    <td><x-amount-format :amount="$user->amount" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="column is-half">
                <div class="box">
                    <h2 class="title is-4 has-text-centered">@lang('leaderboards.best_balance_header')</h2>

                    @php
                        $users = App\Models\User::where('deleted', false)->where('active', true)
                            ->orderByDesc('balance')->limit(10)->get();
                    @endphp

                    <table class="table is-fullwidth">
                        <thead>
                            <tr>
                                <th class="medal-column">#</th>
                                <th>@lang('leaderboards.name')</th>
                                <th style="width: 30%;">@lang('leaderboards.balance')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $index => $user)
                                <tr>
                                    <td><x-index-medal :index="$index" /></td>
                                    <td style="vertical-align: middle;">
                                        <div class="image is-small is-round is-inline" style="background-image: url(/storage/avatars/{{ $user->avatar != null ? $user->avatar : App\Models\Setting::get('default_user_avatar') }});"></div>
                                        {{ $user->name }}
                                    </td>
                                    <td><x-money-format :money="$user->balance" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="column is-half">
                <div class="box">
                    <h2 class="title is-4 has-text-centered">@lang('leaderboards.worst_balance_header')</h2>

                    @php
                        $users = App\Models\User::where('deleted', false)->where('active', true)
                            ->orderBy('balance')->limit(10)->get();
                    @endphp

                    <table class="table is-fullwidth">
                        <thead>
                            <tr>
                                <th class="medal-column">#</th>
                                <th>@lang('leaderboards.name')</th>
                                <th style="width: 30%;">@lang('leaderboards.balance')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $index => $user)
                                <tr>
                                    <td><x-index-medal :index="$index" /></td>
                                    <td style="vertical-align: middle;">
                                        <div class="image is-small is-round is-inline" style="background-image: url(/storage/avatars/{{ $user->avatar != null ? $user->avatar : App\Models\Setting::get('default_user_avatar') }});"></div>
                                        {{ $user->name }}
                                    </td>
                                    <td><x-money-format :money="$user->balance" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @php
                $spendingUsers = App\Models\User::where('deleted', false)->where('active', true)->get()
                    ->map(function ($user) { // Very slow
                        $user->spending = DB::table('transactions')
                            ->where('deleted', false)
                            ->where('user_id', $user->id)
                            ->where('type', App\Models\Transaction::TYPE_TRANSACTION)
                            ->sum('price')
                            + DB::table('transactions')
                            ->where('deleted', false)
                            ->where('user_id', $user->id)
                            ->where('type', App\Models\Transaction::TYPE_FOOD)
                            ->where('price', '>', 0)
                            ->sum('price');
                        return $user;
                    });
            @endphp

            <div class="column is-half">
                <div class="box">
                    <h2 class="title is-4 has-text-centered">@lang('leaderboards.best_spenders_header')</h2>

                    <table class="table is-fullwidth">
                        <thead>
                            <tr>
                                <th class="medal-column">#</th>
                                <th>@lang('leaderboards.name')</th>
                                <th style="width: 30%;">@lang('leaderboards.amount')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($spendingUsers->sortByDesc('spending')->values()->slice(0, 10) as $index => $user)
                                <tr>
                                    <td><x-index-medal :index="$index" /></td>
                                    <td style="vertical-align: middle;">
                                        <div class="image is-small is-round is-inline" style="background-image: url(/storage/avatars/{{ $user->avatar != null ? $user->avatar : App\Models\Setting::get('default_user_avatar') }});"></div>
                                        {{ $user->name }}
                                    </td>
                                    <td><x-money-format :money="$user->spending" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="column is-half">
                <div class="box">
                    <h2 class="title is-4 has-text-centered">@lang('leaderboards.worst_spenders_header')</h2>

                    <table class="table is-fullwidth">
                        <thead>
                            <tr>
                                <th class="medal-column">#</th>
                                <th>@lang('leaderboards.name')</th>
                                <th style="width: 30%;">@lang('leaderboards.amount')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($spendingUsers->sortBy('spending')->values()->slice(0, 10) as $index => $user)
                                <tr>
                                    <td><x-index-medal :index="$index" /></td>
                                    <td style="vertical-align: middle;">
                                        <div class="image is-small is-round is-inline" style="background-image: url(/storage/avatars/{{ $user->avatar != null ? $user->avatar : App\Models\Setting::get('default_user_avatar') }});"></div>
                                        {{ $user->name }}
                                    </td>
                                    <td><x-money-format :money="$user->spending" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endcomponent
