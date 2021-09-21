<?php

namespace App\Console\Commands;

use App\Models\Inventory;
use App\Models\InventoryProduct;
use App\Models\Post;
use App\Models\Product;
use App\Models\User;
use App\Models\Transaction;
use App\Models\TransactionProduct;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

class ImportData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import-data {url}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import data from old strepen system';

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
        $url = $this->argument('url');

        // Refresh database
        Artisan::call('migrate:fresh --seed');

        // Get all the user information
        echo "Importing all users...\n\n";
        $oldUserIds = [ 181 => 2 ];
        $data = file_get_contents($url . '/bonnen/index.php?id=15&newsId=' . urlencode('0 UNION SELECT \'\', \'\', \'\', CONCAT(\'{"id":\', id, \',"name":"\', naam, \'","email":"\', email, \'","active":\', active, \',"receive_news":\', mailinglist, \'}\') FROM stamleden'), false, stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ]));
        preg_match_all('/<p>\{([^\}]+)/m', $data, $usersJson);
        $total = count($usersJson[1]);
        foreach ($usersJson[1] as $index => $userJson) {
            $userJson = json_decode('{' . $userJson . '}');

            $nameParts = explode(' ', $userJson->name);
            $firstname = $nameParts[0];
            array_shift($nameParts);
            $lastname = implode(' ', $nameParts);

            if (!($firstname == 'Bastiaan' && $lastname == 'van der Plaat')) {
                $user = User::where('email', $userJson->email)->first();
                if ($user != null) {
                    $user->active = true;
                    $user->save();
                } else {
                    $user = new User();
                    $user->firstname = $firstname;
                    $user->lastname = $lastname;
                    $user->email = $userJson->email;
                    $user->password = Hash::make('strepen');
                    $user->receive_news = $userJson->receive_news;
                    $user->balance = 0;
                    $user->active = $userJson->active;
                    $user->save();
                }
                $oldUserIds[$userJson->id] = $user->id;
            }
            echo "\033[F" . ($index + 1) . ' / ' . $total . ' = ' . round(($index + 1) / $total * 100, 2) . "%\n";
        }
        echo "Importing users done!\n";
        $users = User::all();

        // Get all the posts information
        echo "Importing all posts...\n\n";
        $data = file_get_contents($url . '/bonnen/index.php?id=15&newsId=' . urlencode('0 UNION SELECT \'\', \'\', id, CONCAT(\'{"title":"\', onderwerp, \'","body":"\', TO_BASE64(bericht), \'","created_at":"\', datum, \'"}\') FROM nieuws'), false, stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ]));
        preg_match_all('/<p>\{([^\}]+)/m', $data, $postsJson);
        $total = count($postsJson[1]);
        foreach ($postsJson[1] as $index => $postJson) {
            $postJson = json_decode('{' . str_replace("\n", '', $postJson) . '}');
            if ($postJson->title == '') {
                $postJson->title = 'Untitled post';
            }
            if ($postJson->body == '') {
                continue;
            }
            $post = new Post();
            $post->user_id = 1;
            $post->title = $postJson->title;
            $post->body = str_replace('<br />', "\n", base64_decode($postJson->body));
            $post->created_at = $postJson->created_at;
            $post->save();
            echo "\033[F" . ($index + 1) . ' / ' . $total . ' = ' . round(($index + 1) / $total * 100, 2) . "%\n";
        }
        echo "Importing posts done!\n";

        // Get all the product information
        echo "Importing all products...\n\n";
        $oldProductIds = [];
        $data = file_get_contents($url . '/bonnen/index.php?id=15&newsId=' . urlencode('0 UNION SELECT \'\', \'\', id, CONCAT(\'{"id":\', id, \',"name":"\', omschrijving, \'","price":\', prijs, \',"active":\', active, \'}\') FROM product'), false, stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ]));
        preg_match_all('/<p>\{([^\}]+)/m', $data, $productsJson);
        $total = count($productsJson[1]);
        foreach ($productsJson[1] as $index => $productJson) {
            $productJson = json_decode('{' . $productJson . '}');
            if ($productJson->id != 7 && $productJson->id != 10) {
                $product = new Product();
                $product->name = $productJson->name;
                $product->price = $productJson->price;
                $product->active = $productJson->active;
                $product->save();
                $oldProductIds[$productJson->id] = $product->id;
            }
            echo "\033[F" . ($index + 1) . ' / ' . $total . ' = ' . round(($index + 1) / $total * 100, 2) . "%\n";
        }
        echo "Importing products done!\n";
        $products = Product::all();

        // Get all inventory information
        echo "Importing all inventories...\n\n";
        $data = file_get_contents($url . '/bonnen/index.php?id=15&newsId=' . urlencode('0 UNION SELECT \'\', \'\', \'\', CONCAT(\'{"id":\', id, \',"old_product_id":\', product_id, \',"amount":\', aantal, \',"action":"\', actie, \'","created_at":"\', datum, \'"}\') FROM inkoop'), false, stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ]));
        preg_match_all('/<p>\{([^\}]+)/m', $data, $inventoriesJson);
        $total = count($inventoriesJson[1]);
        $doneInventories = [];
        foreach ($inventoriesJson[1] as $index => $inventoryJson) {
            $inventoryJson = json_decode('{' . $inventoryJson . '}');
            if (!in_array($inventoryJson->id, $doneInventories)) {
                if ($inventoryJson->amount > 0) {
                    $inventory = new Inventory();
                    $inventory->user_id = 1;
                    $inventory->name = 'Imported inventory on ' . $inventoryJson->created_at;
                    $inventory->price = 0;
                    $inventory->created_at = $inventoryJson->created_at;
                    $inventory->save();

                    for ($i = $index; $i < $index + 50 && $i < $total; $i++) {
                        $otherInventoryJson = json_decode('{' . $inventoriesJson[1][$i] . '}');
                        if (
                            $otherInventoryJson->amount > 0 &&
                            $otherInventoryJson->old_product_id != 7 &&
                            $otherInventoryJson->old_product_id != 10 &&
                            strtotime($otherInventoryJson->created_at) >= strtotime($inventoryJson->created_at) &&
                            strtotime($otherInventoryJson->created_at) < strtotime($inventoryJson->created_at) + 10 * 60
                        ) {
                            $product = $products->firstWhere('id', $oldProductIds[$otherInventoryJson->old_product_id]);
                            $inventory->price += $product->price * $otherInventoryJson->amount;

                            $inventoryProduct = InventoryProduct::where('inventory_id', $inventory->id)
                                ->where('product_id', $product->id)->first();
                            if ($inventoryProduct != null) {
                                $inventory->products()->updateExistingPivot($product->id, [
                                    'amount' => $inventoryProduct->amount + $otherInventoryJson->amount
                                ]);
                            } else {
                                $inventory->products()->attach($product->id, [ 'amount' => $otherInventoryJson->amount ]);
                            }

                            $product->amount += $otherInventoryJson->amount;
                            $product->save();
                            $doneInventories[] = $otherInventoryJson->id;
                        }
                    }
                    $inventory->save();
                }

                if ($inventoryJson->amount < 0) {
                    $transaction = new Transaction();
                    $transaction->user_id = 1;
                    $transaction->type = Transaction::TYPE_TRANSACTION;
                    $transaction->name = 'Imported negative inventory on ' . $inventoryJson->created_at;
                    $transaction->price = 0;
                    $transaction->created_at = $inventoryJson->created_at;
                    $transaction->save();

                    for ($i = $index; $i < $index + 50 && $i < $total; $i++) {
                        $otherInventoryJson = json_decode('{' . $inventoriesJson[1][$i] . '}');
                        if (
                            $otherInventoryJson->amount < 0 &&
                            $otherInventoryJson->old_product_id != 7 &&
                            $otherInventoryJson->old_product_id != 10 &&
                            strtotime($otherInventoryJson->created_at) >= strtotime($inventoryJson->created_at) &&
                            strtotime($otherInventoryJson->created_at) < strtotime($inventoryJson->created_at) + 10 * 60
                        ) {
                            $product = $products->firstWhere('id', $oldProductIds[$otherInventoryJson->old_product_id]);
                            $transaction->price += $product->price * (-$otherInventoryJson->amount);

                            $transactionProduct = TransactionProduct::where('transaction_id', $transaction->id)
                                ->where('product_id', $product->id)->first();
                            if ($transactionProduct != null) {
                                $transaction->products()->updateExistingPivot($product->id, [
                                    'amount' => $transactionProduct->amount + (-$otherInventoryJson->amount)
                                ]);
                            } else {
                                $transaction->products()->attach($product->id, [ 'amount' => -$otherInventoryJson->amount ]);
                            }

                            $product->amount -= -$otherInventoryJson->amount;
                            $product->save();
                            $doneInventories[] = $otherInventoryJson->id;
                        }
                    }
                    $transaction->save();
                }
            }
            echo "\033[F" . ($index + 1) . ' / ' . $total . ' = ' . round(($index + 1) / $total * 100, 2) . "% | " . $inventoryJson->created_at . "\n";
        }
        echo "Importing inventories done!\n";

        // Get all transactions information
        echo "Importing all transactions...\n\n";
        $data = file_get_contents($url . '/bonnen/index.php?id=15&newsId=' . urlencode('0 UNION SELECT \'\', \'\', \'\', CONCAT(\'{"id":\', id, \',"old_user_id":\', lid_id, \',"old_product_id":\', product_id, \',"amount":\', aantal, \',"price":"\', prijs, \'","created_at":"\', datum, \'"}\') FROM schuld'), false, stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ]));
        preg_match_all('/<p>\{([^\}]+)/m', $data, $transactionsJson);
        $total = count($transactionsJson[1]);
        $doneTransactions = [];
        foreach ($transactionsJson[1] as $index => $transactionJson) {
            $transactionJson = json_decode('{' . $transactionJson . '}');
            if (
                !in_array($transactionJson->id, $doneTransactions) &&
                isset($oldUserIds[$transactionJson->old_user_id])
            ) {
                $user = $users->firstWhere('id', $oldUserIds[$transactionJson->old_user_id]);
                if ($transactionJson->price != 0) {
                    if ($transactionJson->old_product_id == 7) {
                        $transaction = new Transaction();
                        $transaction->user_id = $user->id;
                        $transaction->type = Transaction::TYPE_DEPOSIT;
                        $transaction->name = 'Imported deposit on ' . $transactionJson->created_at;
                        $transaction->price = $transactionJson->amount;
                        $transaction->created_at = $transactionJson->created_at;
                        $transaction->save();

                        $user->balance += $transaction->price;
                        $user->save();
                        $doneTransactions[] = $transactionJson->id;
                    } else if ($transactionJson->old_product_id == 10) {
                        $transaction = new Transaction();
                        $transaction->user_id = $user->id;
                        $transaction->type = Transaction::TYPE_FOOD;
                        $transaction->name = 'Imported food transaction on ' . $transactionJson->created_at;
                        $transaction->price = $transactionJson->price;
                        $transaction->created_at = $transactionJson->created_at;
                        $transaction->save();

                        $user->balance -= $transaction->price;
                        $user->save();
                        $doneTransactions[] = $transactionJson->id;
                    } else {
                        $transaction = new Transaction();
                        $transaction->user_id = $user->id;
                        $transaction->type = Transaction::TYPE_TRANSACTION;
                        $transaction->name = 'Imported transaction on ' . $transactionJson->created_at;
                        $transaction->price = 0;
                        $transaction->created_at = $transactionJson->created_at;
                        $transaction->save();

                        for ($i = $index; $i < $index + 50 && $i < $total; $i++) {
                            $otherTransactionJson = json_decode('{' . $transactionsJson[1][$i] . '}');
                            if (
                                isset($oldUserIds[$otherTransactionJson->old_user_id]) &&
                                $oldUserIds[$otherTransactionJson->old_user_id] == $user->id &&
                                $otherTransactionJson->price != 0 &&
                                $otherTransactionJson->old_product_id != 7 &&
                                $otherTransactionJson->old_product_id != 10 &&
                                strtotime($otherTransactionJson->created_at) >= strtotime($transactionJson->created_at) &&
                                strtotime($otherTransactionJson->created_at) < strtotime($transactionJson->created_at) + 10 * 60
                            ) {
                                $product = $products->firstWhere('id', $oldProductIds[$otherTransactionJson->old_product_id]);
                                $transaction->price += $otherTransactionJson->price;

                                $transactionProduct = TransactionProduct::where('transaction_id', $transaction->id)
                                    ->where('product_id', $product->id)->first();
                                if ($transactionProduct != null) {
                                    $transaction->products()->updateExistingPivot($product->id, [
                                        'amount' => $transactionProduct->amount + $otherTransactionJson->amount
                                    ]);
                                } else {
                                    $transaction->products()->attach($product->id, [ 'amount' => $otherTransactionJson->amount ]);
                                }

                                $product->amount -= $otherTransactionJson->amount;
                                $product->save();
                                $doneTransactions[] = $otherTransactionJson->id;
                            }
                        }
                        $transaction->save();
                        $user->balance -= $transaction->price;
                        $user->save();
                    }
                }
            }
            echo "\033[F" . ($index + 1) . ' / ' . $total . ' = ' . round(($index + 1) / $total * 100, 2) . "% | " . $transactionJson->created_at . "\n";
        }
        echo "Importing transactions done!\n";
    }
}
