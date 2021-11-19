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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ImportData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import-data {url} {--export}';

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
        $export = $this->option('export');

        if (!$export) {
            // Refresh database
            Artisan::call('migrate:fresh --seed');
        } else {
            // Create export directory
            $exportDirectory = 'Strepen export on ' . date('Y-m-d H.i.s');
            mkdir($exportDirectory);
        }

        // File get contents context
        $http_context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ]);

        // Get all the user information
        echo "Importing all users...\n\n";
        $oldUserIds = [ 181 => 2 ];
        $data = file_get_contents($url . '/bonnen/index.php?id=15&newsId=' . urlencode('0 UNION SELECT \'\', \'\', \'\', CONCAT(\'{"old_user_id":\', id, \',"name":"\', naam, \'","email":"\', email, \'","active":\', active, \',"receive_news":\', mailinglist, \'}\') FROM stamleden'), false, $http_context);
        preg_match_all('/<p>\{([^\}]+)/m', $data, $itemsJson);
        $usersJson = [];
        foreach ($itemsJson[1] as $itemJson) {
            $usersJson[] = json_decode('{' . $itemJson . '}');
        }

        if ($export) {
            file_put_contents($exportDirectory . '/users.json', json_encode($usersJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        } else {
            $total = count($usersJson);
            foreach ($usersJson as $index => $userJson) {
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
                        $user->password = Hash::make(Str::random(32));
                        $user->receive_news = $userJson->receive_news;
                        $user->balance = 0;
                        $user->active = $userJson->active;
                        $user->checkGravatarAvatar();
                        $user->save();
                    }
                    $oldUserIds[$userJson->old_user_id] = $user->id;
                }
                echo "\033[F" . ($index + 1) . ' / ' . $total . ' = ' . round(($index + 1) / $total * 100, 2) . "%\n";
            }
        }
        echo "Importing users done!\n";

        // Get all the posts information
        echo "Importing all posts...\n\n";
        $data = file_get_contents($url . '/bonnen/index.php?id=15&newsId=' . urlencode('0 UNION SELECT \'\', \'\', id, CONCAT(\'{"title":"\', onderwerp, \'","body":"\', TO_BASE64(bericht), \'","created_at":"\', datum, \'"}\') FROM nieuws'), false, $http_context);
        preg_match_all('/<p>\{([^\}]+)/m', $data, $itemsJson);
        $postsJson = [];
        foreach ($itemsJson[1] as $itemJson) {
            $postJson = json_decode('{' . str_replace("\n", '', $itemJson) . '}');
            $postJson->body = str_replace("\r\n", "\n\n", str_replace('<br />', '', base64_decode($postJson->body)));
            $postsJson[] = $postJson;
        }

        if ($export) {
            file_put_contents($exportDirectory . '/posts.json', json_encode($postsJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        } else {
            $total = count($postsJson);
            foreach ($postsJson as $index => $postJson) {
                if ($postJson->title == '') {
                    $postJson->title = 'Untitled post';
                }
                if ($postJson->body == '') {
                    continue;
                }
                $post = new Post();
                $post->user_id = 1;
                $post->title = $postJson->title;
                $post->body = $postJson->body;
                $post->created_at = $postJson->created_at;
                $post->save();
                echo "\033[F" . ($index + 1) . ' / ' . $total . ' = ' . round(($index + 1) / $total * 100, 2) . "%\n";
            }
        }
        echo "Importing posts done!\n";

        // Get all the product information
        echo "Importing all products...\n\n";
        $oldProductIds = [];
        $data = file_get_contents($url . '/bonnen/index.php?id=15&newsId=' . urlencode('0 UNION SELECT \'\', \'\', id, CONCAT(\'{"old_product_id":\', id, \',"name":"\', omschrijving, \'","price":\', prijs, \',"active":\', active, \'}\') FROM product'), false, $http_context);
        preg_match_all('/<p>\{([^\}]+)/m', $data, $itemsJson);
        $productsJson = [];
        foreach ($itemsJson[1] as $itemJson) {
            $productsJson[] = json_decode('{' . $itemJson . '}');
        }

        if ($export) {
            file_put_contents($exportDirectory . '/products.json', json_encode($productsJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        } else {
            $total = count($productsJson);
            foreach ($productsJson as $index => $productJson) {
                if ($productJson->old_product_id != 7 && $productJson->old_product_id != 10) {
                    $product = new Product();
                    $product->name = $productJson->name;
                    $product->price = $productJson->price;
                    $product->active = $productJson->active;
                    $product->alcoholic = true;
                    $product->save();
                    $oldProductIds[$productJson->old_product_id] = $product->id;
                }
                echo "\033[F" . ($index + 1) . ' / ' . $total . ' = ' . round(($index + 1) / $total * 100, 2) . "%\n";
            }

            $products = Product::all();
        }
        echo "Importing products done!\n";

        // Get all inventory information
        echo "Importing all inventories...\n\n";
        $data = file_get_contents($url . '/bonnen/index.php?id=15&newsId=' . urlencode('0 UNION SELECT \'\', \'\', \'\', CONCAT(\'{"old_inventory_id":\', id, \',"old_product_id":\', product_id, \',"amount":\', aantal, \',"action":"\', actie, \'","created_at":"\', datum, \'"}\') FROM inkoop'), false, $http_context);
        preg_match_all('/<p>\{([^\}]+)/m', $data, $itemsJson);
        $inventoriesJson = [];
        foreach ($itemsJson[1] as $itemJson) {
            $inventoriesJson[] = json_decode('{' . $itemJson . '}');
        }

        if ($export) {
            file_put_contents($exportDirectory . '/inventories.json', json_encode($inventoriesJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        } else {
            $total = count($inventoriesJson);
            $doneInventories = [];
            foreach ($inventoriesJson as $inventoryJson) {
                $inventoryJson->created_at_timestamp = strtotime($inventoryJson->created_at);
            }

            foreach ($inventoriesJson as $index => $inventoryJson) {
                if (!in_array($inventoryJson->old_inventory_id, $doneInventories)) {
                    if ($inventoryJson->amount > 0) {
                        $inventory = null;
                        for ($i = $index; $i < $index + 25 && $i < $total; $i++) {
                            $otherInventoryJson = $inventoriesJson[$i];
                            if (
                                !in_array($otherInventoryJson->old_inventory_id, $doneInventories) &&
                                $otherInventoryJson->amount > 0 &&
                                $otherInventoryJson->old_product_id != 7 &&
                                $otherInventoryJson->old_product_id != 10 &&
                                $otherInventoryJson->created_at_timestamp >= $inventoryJson->created_at_timestamp &&
                                $otherInventoryJson->created_at_timestamp < $inventoryJson->created_at_timestamp + 60
                            ) {
                                if ($inventory == null) {
                                    $inventory = new Inventory();
                                    $inventory->user_id = 1;
                                    $inventory->name = 'Imported inventory on ' . $inventoryJson->created_at;
                                    $inventory->price = 0;
                                    $inventory->created_at = $inventoryJson->created_at;
                                    $inventory->save();
                                }

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

                                $doneInventories[] = $otherInventoryJson->old_inventory_id;
                            }
                        }
                        if ($inventory != null) {
                            $inventory->save();
                        }
                    }

                    if ($inventoryJson->amount < 0) {
                        $transaction = null;
                        for ($i = $index; $i < $index + 25 && $i < $total; $i++) {
                            $otherInventoryJson = $inventoriesJson[$i];
                            if (
                                !in_array($otherInventoryJson->old_inventory_id, $doneInventories) &&
                                $otherInventoryJson->amount < 0 &&
                                $otherInventoryJson->old_product_id != 7 &&
                                $otherInventoryJson->old_product_id != 10 &&
                                $otherInventoryJson->created_at_timestamp >= $inventoryJson->created_at_timestamp &&
                                $otherInventoryJson->created_at_timestamp < $inventoryJson->created_at_timestamp + 60
                            ) {
                                if ($transaction == null) {
                                    $transaction = new Transaction();
                                    $transaction->user_id = 1;
                                    $transaction->type = Transaction::TYPE_TRANSACTION;
                                    $transaction->name = 'Imported negative inventory on ' . $inventoryJson->created_at;
                                    $transaction->price = 0;
                                    $transaction->created_at = $inventoryJson->created_at;
                                    $transaction->save();
                                }

                                $otherInventoryJson->amount = -$otherInventoryJson->amount;
                                $product = $products->firstWhere('id', $oldProductIds[$otherInventoryJson->old_product_id]);
                                $transaction->price += $product->price * $otherInventoryJson->amount;

                                $transactionProduct = TransactionProduct::where('transaction_id', $transaction->id)
                                    ->where('product_id', $product->id)->first();
                                if ($transactionProduct != null) {
                                    $transaction->products()->updateExistingPivot($product->id, [
                                        'amount' => $transactionProduct->amount + $otherInventoryJson->amount
                                    ]);
                                } else {
                                    $transaction->products()->attach($product->id, [ 'amount' => $otherInventoryJson->amount ]);
                                }

                                $doneInventories[] = $otherInventoryJson->old_inventory_id;
                            }
                        }
                        if ($transaction != null) {
                            $transaction->save();
                        }
                    }
                }
                echo "\033[F" . ($index + 1) . ' / ' . $total . ' = ' . round(($index + 1) / $total * 100, 2) . "% | " . $inventoryJson->created_at . "\n";
            }
        }
        echo "Importing inventories done!\n";

        // Get all transactions information
        echo "Importing all transactions...\n\n";
        $data = file_get_contents($url . '/bonnen/index.php?id=15&newsId=' . urlencode('0 UNION SELECT \'\', \'\', \'\', CONCAT(\'{"old_transaction_id":\', id, \',"old_user_id":\', lid_id, \',"old_product_id":\', product_id, \',"amount":\', aantal, \',"price":"\', prijs, \'","created_at":"\', datum, \'"}\') FROM schuld'), false, $http_context);
        preg_match_all('/<p>\{([^\}]+)/m', $data, $itemsJson);
        $transactionsJson = [];
        foreach ($itemsJson[1] as $itemJson) {
            $transactionsJson[] = json_decode('{' . $itemJson . '}');
        }

        function createTransactionProduct($transactionProduct) {
            static $transactionProductsCache = [];
            if ($transactionProduct != null) {
                $transactionProductsCache[] = $transactionProduct;
            }
            if ($transactionProduct == null || count($transactionProductsCache) == 500) {
                TransactionProduct::insert($transactionProductsCache);
                $transactionProductsCache = [];
            }
        }

        function createTransaction($transaction, $products = []) {
            static $transactionsCache = [];
            static $productsCache = [];
            if ($transaction != null) {
                $transactionsCache[] = $transaction;
                $productsCache[] = $products;
            }
            if ($transaction == null || count($transactionsCache) == 500) {
                $transaction_id = Transaction::orderBy('id', 'DESC')->first()->id + 1;
                Transaction::insert($transactionsCache);
                foreach ($transactionsCache as $index => $transaction) {
                    $products = $productsCache[$index];
                    foreach ($products as $transactionProduct) {
                        $transactionProduct['transaction_id'] = $transaction_id;
                        createTransactionProduct($transactionProduct);
                    }
                    $transaction_id++;
                }
                $transactionsCache = [];
                $productsCache = [];
            }
        }

        if ($export) {
            file_put_contents($exportDirectory . '/transactions.json', json_encode($transactionsJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        } else {
            $total = count($transactionsJson);
            $doneTransactions = [];
            foreach ($transactionsJson as $transactionJson) {
                $transactionJson->created_at_timestamp = strtotime($transactionJson->created_at);
            }

            foreach ($transactionsJson as $index => $transactionJson) {
                if (!in_array($transactionJson->old_transaction_id, $doneTransactions)) {
                    if ($transactionJson->old_product_id == 7) {
                        if ($transactionJson->price != 0) {
                            $transactionJson->price = -$transactionJson->price;
                            createTransaction([
                                'user_id' => $oldUserIds[$transactionJson->old_user_id],
                                'type' => Transaction::TYPE_DEPOSIT,
                                'name' => 'Imported deposit on ' . $transactionJson->created_at,
                                'price' => $transactionJson->price,
                                'created_at' => $transactionJson->created_at
                            ]);
                            $doneTransactions[] = $transactionJson->old_transaction_id;
                        }
                    } else if ($transactionJson->old_product_id == 10) {
                        if ($transactionJson->price != 0) {
                            createTransaction([
                                'user_id' => $oldUserIds[$transactionJson->old_user_id],
                                'type' => Transaction::TYPE_FOOD,
                                'name' => 'Imported food transaction on ' . $transactionJson->created_at,
                                'price' => $transactionJson->price,
                                'created_at' => $transactionJson->created_at
                            ]);
                            $doneTransactions[] = $transactionJson->old_transaction_id;
                        }
                    } else {
                        $transactionProducts = [];
                        $transactionPrice = 0;
                        for ($i = $index; $i < $index + 25 && $i < $total; $i++) {
                            $otherTransactionJson = $transactionsJson[$i];
                            if (
                                $oldUserIds[$otherTransactionJson->old_user_id] == $oldUserIds[$transactionJson->old_user_id] &&
                                $otherTransactionJson->old_product_id != 7 &&
                                $otherTransactionJson->old_product_id != 10 &&
                                $otherTransactionJson->created_at_timestamp >= $transactionJson->created_at_timestamp &&
                                $otherTransactionJson->created_at_timestamp < $transactionJson->created_at_timestamp + 60
                            ) {
                                if ($otherTransactionJson->amount > 0) {
                                    $alreadyExists = false;
                                    foreach ($transactionProducts as &$transactionProduct) {
                                        if ($transactionProduct['product_id'] == $oldProductIds[$otherTransactionJson->old_product_id]) {
                                            $transactionProduct['amount'] += $otherTransactionJson->amount;
                                            $alreadyExists = true;
                                            break;
                                        }
                                    }
                                    if (!$alreadyExists) {
                                        $transactionProducts[] = [
                                            'product_id' => $oldProductIds[$otherTransactionJson->old_product_id],
                                            'amount' => $otherTransactionJson->amount
                                        ];
                                    }
                                    $transactionPrice += $otherTransactionJson->price;
                                }
                                $doneTransactions[] = $otherTransactionJson->old_transaction_id;
                            }
                        }
                        if (count($transactionProducts) == 0) {
                            continue;
                        }

                        createTransaction([
                            'user_id' => $oldUserIds[$transactionJson->old_user_id],
                            'type' => Transaction::TYPE_TRANSACTION,
                            'name' => 'Imported transaction on ' . $transactionJson->created_at,
                            'price' => $transactionPrice,
                            'created_at' => $transactionJson->created_at
                        ], $transactionProducts);
                    }
                }
                echo "\033[F" . ($index + 1) . ' / ' . $total . ' = ' . round(($index + 1) / $total * 100, 2) . "% | " . $transactionJson->created_at . "\n";
            }

            createTransaction(null);
            createTransactionProduct(null);
        }
        echo "Importing transactions done!\n";

        // Recalculate all amounts and balances
        if (!$export) {
            Artisan::call('recalculate');
        }
    }
}
