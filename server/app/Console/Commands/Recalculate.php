<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\User;
use Illuminate\Console\Command;

class Recalculate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recalculate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate all user balances and all product amounts';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        echo "Recalculate user balances...\n";
        foreach (User::all() as $user) {
            $user->recalculateBalance();
            $user->save();
            echo $user->name . ': $ ' . $user->balance . "\n";
        }
        echo "Recalculate user balances done\n\n";

        echo "Recalculate product amounts...\n";
        foreach (Product::all() as $product) {
            $product->recalculateAmount();
            $product->save();
            echo $product->name . ': ' . $product->amount . " x\n";
        }
        echo "Recalculate product amounts done\n";
    }
}
