<div class="container content">
    @if ($enabled || Auth::user()->role == App\Models\User::ROLE_MANAGER || Auth::user()->role == App\Models\User::ROLE_ADMIN)
        <div class="columns">
            <div class="column is-two-thirds">
                <h2 class="title is-4">@lang('leaderboards.header')</h2>
            </div>
            <div class="column is-one-third">
                <form wire:submit.prevent="select">
                    <div class="field has-addons is-block-mobile">
                        <div class="control" style="width: 100%;">
                            <div class="select is-fullwidth">
                                <select wire:model.defer="range">
                                    <option value="month_to_date">@lang('leaderboards.range_chooser_month_to_date') (@lang('leaderboards.range_chooser_from') {{ date('Y-m-01') }})</option>
                                    <option value="month">@lang('leaderboards.range_chooser_month') (@lang('leaderboards.range_chooser_from') {{ date('Y-m-d', time() - 30 * 24 * 60 * 60) }})</option>
                                    <option value="half_year">@lang('leaderboards.range_chooser_half_year') (@lang('leaderboards.range_chooser_from') {{ date('Y-m-d', time() - 182 * 24 * 60 * 60) }})</option>
                                    <option value="null">@lang('leaderboards.range_chooser_year_to_date') (@lang('leaderboards.range_chooser_from') {{ date('Y-01-01') }})</option>
                                    <option value="year">@lang('leaderboards.range_chooser_year') (@lang('leaderboards.range_chooser_from') {{ date('Y-m-d', time() - 365 * 24 * 60 * 60) }})</option>
                                    <option value="two_year">@lang('leaderboards.range_chooser_two_year') (@lang('leaderboards.range_chooser_from') {{ date('Y-m-d', time() - 2 * 365 * 24 * 60 * 60) }})</option>
                                    <option value="five_year">@lang('leaderboards.range_chooser_five_year') (@lang('leaderboards.range_chooser_from') {{ date('Y-m-d', time() - 5 * 365 * 24 * 60 * 60) }})</option>
                                    <option value="everything">@lang('leaderboards.range_chooser_everything') (@lang('leaderboards.range_chooser_from') {{ $oldestItemDate }})</option>
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
                        $beerUsers = App\Models\User::where('active', true)->get()
                            ->map(function ($user) use ($beerProductId, $startDate) { // Very slow
                                $user->amount = DB::table('transaction_product')
                                    ->join('transactions', 'transactions.id', 'transaction_id')
                                    ->whereNull('deleted_at')
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
                                <th style="width: @if ($range != 'month_to_date' && $range != 'month') 20% @else 40% @endif;">
                                    @lang('leaderboards.amount')
                                </th>
                                @if ($range == 'half_year' || $range == null || $range == 'year')
                                    <th style="width: 20%;">@lang('leaderboards.change_month')</th>
                                @endif
                                @if ($range == 'two_year' || $range == 'five_year' || $range == 'everything')
                                    <th style="width: 20%;">@lang('leaderboards.change_year')</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($beerUsers as $index => $user)
                                <tr>
                                    <td><x-index-medal :index="$index" /></td>
                                    <td style="vertical-align: middle;">
                                        <div class="image is-small is-round is-inline" style="background-image: url(/storage/avatars/{{ $user->avatar ?? App\Models\Setting::get('default_user_avatar') }});"></div>
                                        {{ $user->name }}
                                    </td>
                                    <td><x-amount-format :amount="$user->amount" /></td>

                                    @if ($range == 'half_year' || $range == null || $range == 'year')
                                        <td>
                                            <x-change-format :change="DB::table('transaction_product')
                                                ->join('transactions', 'transactions.id', 'transaction_id')
                                                ->whereNull('deleted_at')
                                                ->where('user_id', $user->id)
                                                ->where('product_id', $beerProductId)
                                                ->where('transactions.created_at', '>=', date('Y-m-d', time() - 30 * 24 * 60 * 60))
                                                ->sum('amount')" />
                                        </td>
                                    @endif

                                    @if ($range == 'two_year' || $range == 'five_year' || $range == 'everything')
                                        <td>
                                            <x-change-format :change="DB::table('transaction_product')
                                                ->join('transactions', 'transactions.id', 'transaction_id')
                                                ->whereNull('deleted_at')
                                                ->where('user_id', $user->id)
                                                ->where('product_id', $beerProductId)
                                                ->where('transactions.created_at', '>=', date('Y-m-d', time() - 365 * 24 * 60 * 60))
                                                ->sum('amount')" />
                                        </td>
                                    @endif
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
                        $sodaUsers = App\Models\User::where('active', true)->get()
                            ->map(function ($user) use ($sodaProductId, $startDate) { // Very slow
                                $user->amount = DB::table('transaction_product')
                                    ->join('transactions', 'transactions.id', 'transaction_id')
                                    ->whereNull('deleted_at')
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
                                <th style="width: @if ($range != 'month_to_date' && $range != 'month') 20% @else 40% @endif;">
                                    @lang('leaderboards.amount')
                                </th>
                                @if ($range == 'half_year' || $range == null || $range == 'year')
                                    <th style="width: 20%;">@lang('leaderboards.change_month')</th>
                                @endif
                                @if ($range == 'two_year' || $range == 'five_year' || $range == 'everything')
                                    <th style="width: 20%;">@lang('leaderboards.change_year')</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sodaUsers as $index => $user)
                                <tr>
                                    <td><x-index-medal :index="$index" /></td>
                                    <td style="vertical-align: middle;">
                                        <div class="image is-small is-round is-inline" style="background-image: url(/storage/avatars/{{ $user->avatar ?? App\Models\Setting::get('default_user_avatar') }});"></div>
                                        {{ $user->name }}
                                    </td>
                                    <td><x-amount-format :amount="$user->amount" /></td>

                                    @if ($range == 'half_year' || $range == null || $range == 'year')
                                        <td>
                                            <x-change-format :change="DB::table('transaction_product')
                                                ->join('transactions', 'transactions.id', 'transaction_id')
                                                ->whereNull('deleted_at')
                                                ->where('user_id', $user->id)
                                                ->where('product_id', $sodaProductId)
                                                ->where('transactions.created_at', '>=', date('Y-m-d', time() - 30 * 24 * 60 * 60))
                                                ->sum('amount')" />
                                        </td>
                                    @endif

                                    @if ($range == 'two_year' || $range == 'five_year' || $range == 'everything')
                                        <td>
                                            <x-change-format :change="DB::table('transaction_product')
                                                ->join('transactions', 'transactions.id', 'transaction_id')
                                                ->whereNull('deleted_at')
                                                ->where('user_id', $user->id)
                                                ->where('product_id', $sodaProductId)
                                                ->where('transactions.created_at', '>=', date('Y-m-d', time() - 365 * 24 * 60 * 60))
                                                ->sum('amount')" />
                                        </td>
                                    @endif
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
                        $snackUsers = App\Models\User::where('active', true)->get()
                            ->map(function ($user) use ($candybarProductId, $chipsProductId, $startDate) { // Very slow
                                $user->amount = DB::table('transaction_product')
                                    ->join('transactions', 'transactions.id', 'transaction_id')
                                    ->whereNull('deleted_at')
                                    ->where('user_id', $user->id)
                                    ->where(fn ($query) => $query->where('product_id', $candybarProductId)
                                        ->orWhere('product_id', $chipsProductId))
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
                                <th style="width: @if ($range != 'month_to_date' && $range != 'month') 20% @else 40% @endif;">
                                    @lang('leaderboards.amount')
                                </th>
                                @if ($range == 'half_year' || $range == null || $range == 'year')
                                    <th style="width: 20%;">@lang('leaderboards.change_month')</th>
                                @endif
                                @if ($range == 'two_year' || $range == 'five_year' || $range == 'everything')
                                    <th style="width: 20%;">@lang('leaderboards.change_year')</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($snackUsers as $index => $user)
                                <tr>
                                    <td><x-index-medal :index="$index" /></td>
                                    <td style="vertical-align: middle;">
                                        <div class="image is-small is-round is-inline" style="background-image: url(/storage/avatars/{{ $user->avatar ?? App\Models\Setting::get('default_user_avatar') }});"></div>
                                        {{ $user->name }}
                                    </td>
                                    <td><x-amount-format :amount="$user->amount" /></td>

                                    @if ($range == 'half_year' || $range == null || $range == 'year')
                                        <td>
                                            <x-change-format :change="DB::table('transaction_product')
                                                ->join('transactions', 'transactions.id', 'transaction_id')
                                                ->whereNull('deleted_at')
                                                ->where('user_id', $user->id)
                                                ->where(fn ($query) => $query->where('product_id', $candybarProductId)
                                                    ->orWhere('product_id', $chipsProductId))
                                                ->where('transactions.created_at', '>=', date('Y-m-d', time() - 30 * 24 * 60 * 60))
                                                ->sum('amount')" />
                                        </td>
                                    @endif

                                    @if ($range == 'two_year' || $range == 'five_year' || $range == 'everything')
                                        <td>
                                            <x-change-format :change="DB::table('transaction_product')
                                                ->join('transactions', 'transactions.id', 'transaction_id')
                                                ->whereNull('deleted_at')
                                                ->where('user_id', $user->id)
                                                ->where(fn ($query) => $query->where('product_id', $candybarProductId)
                                                    ->orWhere('product_id', $chipsProductId))
                                                ->where('transactions.created_at', '>=', date('Y-m-d', time() - 365 * 24 * 60 * 60))
                                                ->sum('amount')" />
                                        </td>
                                    @endif
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
                        $spendingUsers = App\Models\User::where('active', true)->get()
                            ->map(function ($user) use ($startDate) { // Very slow
                                $user->spending = DB::table('transactions')
                                    ->whereNull('deleted_at')
                                    ->where('user_id', $user->id)
                                    ->where('type', App\Models\Transaction::TYPE_TRANSACTION)
                                    ->where('transactions.created_at', '>=', $startDate)
                                    ->sum('price')
                                    + DB::table('transactions')
                                    ->whereNull('deleted_at')
                                    ->where('user_id', $user->id)
                                    ->where('type', App\Models\Transaction::TYPE_FOOD)
                                    ->where('price', '>', 0)
                                    ->where('transactions.created_at', '>=', $startDate)
                                    ->sum('price');
                                return $user;
                            })
                            ->sortByDesc('spending')->values()
                            ->slice(0, 10);
                    @endphp

                    <table class="table is-fullwidth">
                        <thead>
                            <tr>
                                <th class="medal-column">#</th>
                                <th>@lang('leaderboards.name')</th>
                                <th style="width: @if ($range != 'month_to_date' && $range != 'month') 20% @else 40% @endif;">
                                    @lang('leaderboards.cost')
                                </th>
                                @if ($range == 'half_year' || $range == null || $range == 'year')
                                    <th style="width: 20%;">@lang('leaderboards.change_month')</th>
                                @endif
                                @if ($range == 'two_year' || $range == 'five_year' || $range == 'everything')
                                    <th style="width: 20%;">@lang('leaderboards.change_year')</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($spendingUsers as $index => $user)
                                <tr>
                                    <td><x-index-medal :index="$index" /></td>
                                    <td style="vertical-align: middle;">
                                        <div class="image is-small is-round is-inline" style="background-image: url(/storage/avatars/{{ $user->avatar ?? App\Models\Setting::get('default_user_avatar') }});"></div>
                                        {{ $user->name }}
                                    </td>
                                    <td><x-money-format :money="$user->spending" /></td>

                                    @if ($range == 'half_year' || $range == null || $range == 'year')
                                        <td>
                                            <x-change-format :change="DB::table('transactions')
                                                ->whereNull('deleted_at')
                                                ->where('user_id', $user->id)
                                                ->where('type', App\Models\Transaction::TYPE_TRANSACTION)
                                                ->where('created_at', '>=', date('Y-m-d', time() - 30 * 24 * 60 * 60))
                                                ->sum('price')
                                                + DB::table('transactions')
                                                ->whereNull('deleted_at')
                                                ->where('user_id', $user->id)
                                                ->where('type', App\Models\Transaction::TYPE_FOOD)
                                                ->where('price', '>', 0)
                                                ->where('created_at', '>=', date('Y-m-d', time() - 30 * 24 * 60 * 60))
                                                ->sum('price')" isMoney="true" />
                                        </td>
                                    @endif

                                    @if ($range == 'two_year' || $range == 'five_year' || $range == 'everything')
                                        <td>
                                            <x-change-format :change="DB::table('transactions')
                                                ->whereNull('deleted_at')
                                                ->where('user_id', $user->id)
                                                ->where('type', App\Models\Transaction::TYPE_TRANSACTION)
                                                ->where('created_at', '>=', date('Y-m-d', time() - 365 * 24 * 60 * 60))
                                                ->sum('price')
                                                + DB::table('transactions')
                                                ->whereNull('deleted_at')
                                                ->where('user_id', $user->id)
                                                ->where('type', App\Models\Transaction::TYPE_FOOD)
                                                ->where('price', '>', 0)
                                                ->where('created_at', '>=', date('Y-m-d', time() - 365 * 24 * 60 * 60))
                                                ->sum('price')" isMoney="true" />
                                        </td>
                                    @endif
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
                        $users = App\Models\User::where('active', true)
                            ->orderByDesc('balance')->limit(10)->get();
                    @endphp

                    <table class="table is-fullwidth">
                        <thead>
                            <tr>
                                <th class="medal-column">#</th>
                                <th>@lang('leaderboards.name')</th>
                                <th style="width: @if ($range != 'month_to_date' && $range != 'month') 20% @else 40% @endif;">
                                    @lang('leaderboards.balance')
                                </th>
                                @if ($range == 'half_year' || $range == null || $range == 'year')
                                    <th style="width: 20%;">@lang('leaderboards.change_month')</th>
                                @endif
                                @if ($range == 'two_year' || $range == 'five_year' || $range == 'everything')
                                    <th style="width: 20%;">@lang('leaderboards.change_year')</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $index => $user)
                                <tr>
                                    <td><x-index-medal :index="$index" /></td>
                                    <td style="vertical-align: middle;">
                                        <div class="image is-small is-round is-inline" style="background-image: url(/storage/avatars/{{ $user->avatar ?? App\Models\Setting::get('default_user_avatar') }});"></div>
                                        {{ $user->name }}
                                    </td>
                                    <td><x-money-format :money="$user->balance" /></td>

                                    @if ($range == 'half_year' || $range == null || $range == 'year')
                                        <td>
                                            <x-change-format :change="DB::table('transactions')
                                                ->whereNull('deleted_at')
                                                ->where('user_id', $user->id)
                                                ->where('type', App\Models\Transaction::TYPE_DEPOSIT)
                                                ->where('created_at', '>=', date('Y-m-d', time() - 30 * 24 * 60 * 60))
                                                ->sum('price')
                                                -
                                                DB::table('transactions')
                                                ->whereNull('deleted_at')
                                                ->where('user_id', $user->id)
                                                ->where(fn ($query) => $query->where('type', App\Models\Transaction::TYPE_TRANSACTION)
                                                    ->orWhere('type', App\Models\Transaction::TYPE_FOOD))
                                                ->where('created_at', '>=', date('Y-m-d', time() - 30 * 24 * 60 * 60))
                                                ->sum('price')" isMoney="true" />
                                        </td>
                                    @endif

                                    @if ($range == 'two_year' || $range == 'five_year' || $range == 'everything')
                                        <td>
                                            <x-change-format :change="DB::table('transactions')
                                                ->whereNull('deleted_at')
                                                ->where('user_id', $user->id)
                                                ->where('type', App\Models\Transaction::TYPE_DEPOSIT)
                                                ->where('created_at', '>=', date('Y-m-d', time() - 365 * 24 * 60 * 60))
                                                ->sum('price')
                                                -
                                                DB::table('transactions')
                                                ->whereNull('deleted_at')
                                                ->where('user_id', $user->id)
                                                ->where(fn ($query) => $query->where('type', App\Models\Transaction::TYPE_TRANSACTION)
                                                    ->orWhere('type', App\Models\Transaction::TYPE_FOOD))
                                                ->where('created_at', '>=', date('Y-m-d', time() - 365 * 24 * 60 * 60))
                                                ->sum('price')" isMoney="true" />
                                        </td>
                                    @endif
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
                        $users = App\Models\User::where('active', true)
                            ->orderBy('balance')->limit(10)->get();
                    @endphp

                    <table class="table is-fullwidth">
                        <thead>
                            <tr>
                                <th class="medal-column">#</th>
                                <th>@lang('leaderboards.name')</th>
                                <th style="width: @if ($range != 'month_to_date' && $range != 'month') 20% @else 40% @endif;">
                                    @lang('leaderboards.balance')
                                </th>
                                @if ($range == 'half_year' || $range == null || $range == 'year')
                                    <th style="width: 20%;">@lang('leaderboards.change_month')</th>
                                @endif
                                @if ($range == 'two_year' || $range == 'five_year' || $range == 'everything')
                                    <th style="width: 20%;">@lang('leaderboards.change_year')</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $index => $user)
                                <tr>
                                    <td><x-index-medal :index="$index" /></td>
                                    <td style="vertical-align: middle;">
                                        <div class="image is-small is-round is-inline" style="background-image: url(/storage/avatars/{{ $user->avatar ?? App\Models\Setting::get('default_user_avatar') }});"></div>
                                        {{ $user->name }}
                                    </td>
                                    <td><x-money-format :money="$user->balance" /></td>

                                    @if ($range == 'half_year' || $range == null || $range == 'year')
                                        <td>
                                            <x-change-format :change="DB::table('transactions')
                                                ->whereNull('deleted_at')
                                                ->where('user_id', $user->id)
                                                ->where('type', App\Models\Transaction::TYPE_DEPOSIT)
                                                ->where('created_at', '>=', date('Y-m-d', time() - 30 * 24 * 60 * 60))
                                                ->sum('price')
                                                -
                                                DB::table('transactions')
                                                ->whereNull('deleted_at')
                                                ->where('user_id', $user->id)
                                                ->where(fn ($query) => $query->where('type', App\Models\Transaction::TYPE_TRANSACTION)
                                                    ->orWhere('type', App\Models\Transaction::TYPE_FOOD))
                                                ->where('created_at', '>=', date('Y-m-d', time() - 30 * 24 * 60 * 60))
                                                ->sum('price')" isMoney="true" />
                                        </td>
                                    @endif

                                    @if ($range == 'two_year' || $range == 'five_year' || $range == 'everything')
                                        <td>
                                            <x-change-format :change="DB::table('transactions')
                                                ->whereNull('deleted_at')
                                                ->where('user_id', $user->id)
                                                ->where('type', App\Models\Transaction::TYPE_DEPOSIT)
                                                ->where('created_at', '>=', date('Y-m-d', time() - 365 * 24 * 60 * 60))
                                                ->sum('price')
                                                -
                                                DB::table('transactions')
                                                ->whereNull('deleted_at')
                                                ->where('user_id', $user->id)
                                                ->where(fn ($query) => $query->where('type', App\Models\Transaction::TYPE_TRANSACTION)
                                                    ->orWhere('type', App\Models\Transaction::TYPE_FOOD))
                                                ->where('created_at', '>=', date('Y-m-d', time() - 365 * 24 * 60 * 60))
                                                ->sum('price')" isMoney="true" />
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="container content">
            <h1 class="title">@lang('leaderboards.disabled_header')</h1>
            <p>@lang('leaderboards.disabled_info')</p>
        </div>
    @endif
</div>
