<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CheckBalances extends Command
{
    protected $signature = 'check-balances';

    protected $description = 'Check user balances and send notifications if to low';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): int
    {
        echo "Checking users balances...\n";
        User::checkBalances();
        echo "Checking users balances done\n";
        return 0;
    }
}
