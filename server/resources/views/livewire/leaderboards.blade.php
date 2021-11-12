<div class="container content">
    <div class="columns">
        <div class="column is-two-thirds">
            <h2 class="title is-4">@lang('leaderboards.header')</h2>
        </div>
        <div class="column is-one-third">
            <form wire:submit.prevent="select">
                <div class="field has-addons is-block-mobile">
                    <div class="control" style="width: 100%;">
                        <div class="select is-fullwidth">
                            <select wire:model.defer="type">
                                <option value="month">@lang('leaderboards.type_chooser_month')</option>
                                <option value="half_year">@lang('leaderboards.type_chooser_half_year')</option>
                                <option value="null">@lang('leaderboards.type_chooser_year_to_date')</option>
                                <option value="year">@lang('leaderboards.type_chooser_year')</option>
                                <option value="two_year">@lang('leaderboards.type_chooser_two_year')</option>
                                <option value="five_year">@lang('leaderboards.type_chooser_five_year')</option>
                                <option value="everything">@lang('leaderboards.type_chooser_everything')</option>
                            </select>
                        </div>
                    </div>
                    <div class="control">
                        <button class="button is-link" type="submit" style="width: 100%;">@lang('leaderboards.select')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="columns is-multiline">
        <div class="column is-half">
            <div class="box">
                <h2 class="title is-4 has-text-centered">@lang('leaderboards.best_beer_header')</h2>

                @php
                    $beerProductId = 1; // Weird constant
                    $beerUsers = App\Models\User::where('deleted', false)->where('active', true)->get()
                        ->map(function ($user) use ($beerProductId, $startDate) { // Very slow
                            $user->amount = DB::table('transaction_product')
                                ->join('transactions', 'transactions.id', 'transaction_id')
                                ->where('deleted', false)
                                ->where('user_id', $user->id)
                                ->where('product_id', $beerProductId)
                                ->where('transactions.created_at', '>=', $startDate)
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
                        ->map(function ($user) use ($sodaProductId, $startDate) { // Very slow
                            $user->amount = DB::table('transaction_product')
                                ->join('transactions', 'transactions.id', 'transaction_id')
                                ->where('deleted', false)
                                ->where('user_id', $user->id)
                                ->where('product_id', $sodaProductId)
                                ->where('transactions.created_at', '>=', $startDate)
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
                <h2 class="title is-4 has-text-centered">@lang('leaderboards.best_snack_header')</h2>

                @php
                    $candybarProductId = 3; // Weird constant
                    $chipsProductId = 4; // Weird constant
                    $sodaUsers = App\Models\User::where('deleted', false)->where('active', true)->get()
                        ->map(function ($user) use ($candybarProductId, $chipsProductId, $startDate) { // Very slow
                            $user->amount = DB::table('transaction_product')
                                ->join('transactions', 'transactions.id', 'transaction_id')
                                ->where('deleted', false)
                                ->where('user_id', $user->id)
                                ->where(function ($query) use ($candybarProductId, $chipsProductId) {
                                    $query->where('product_id', $candybarProductId)
                                        ->orWhere('product_id', $chipsProductId);
                                })
                                ->where('transactions.created_at', '>=', $startDate)
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
                <h2 class="title is-4 has-text-centered">@lang('leaderboards.best_spenders_header')</h2>

                @php
                    $spendingUsers = App\Models\User::where('deleted', false)->where('active', true)->get()
                        ->map(function ($user) use ($startDate) { // Very slow
                            $user->spending = DB::table('transactions')
                                ->where('deleted', false)
                                ->where('user_id', $user->id)
                                ->where('type', App\Models\Transaction::TYPE_TRANSACTION)
                                ->where('transactions.created_at', '>=', $startDate)
                                ->sum('price')
                                + DB::table('transactions')
                                ->where('deleted', false)
                                ->where('user_id', $user->id)
                                ->where('type', App\Models\Transaction::TYPE_FOOD)
                                ->where('price', '>', 0)
                                ->where('transactions.created_at', '>=', $startDate)
                                ->sum('price');
                            return $user;
                        });
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
    </div>
</div>
