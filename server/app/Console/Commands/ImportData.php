<?php

namespace App\Console\Commands;

use App\Models\Inventory;
use App\Models\Post;
use App\Models\Product;
use App\Models\User;
use App\Models\Transaction;
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
        echo "Importing all active users...\n\n";
        $oldUserIds = [ 181 => 2 ];
        $data = file_get_contents($url . '/bonnen/index.php?id=15&newsId=' . urlencode('0 UNION SELECT \'\', \'\', \'\', CONCAT(\'{"id":\', id, \',"name":"\', naam, \'","email":"\', email, \'","active":\', active, \'}\') FROM stamleden'), false, stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ]));
        preg_match_all('/<p>\{([^\}]+)/m', $data, $usersJson);
        $total = count($usersJson[1]);
        foreach ($usersJson[1] as $index => $userJson) {
            $user = json_decode('{' . $userJson . '}');
            $nameParts = explode(' ', $user->name);
            $firstname = $nameParts[0];
            array_shift($nameParts);
            $lastname = implode(' ', $nameParts);
            if ($user->active && $lastname != 'van der Plaat') {
                $userModel = User::create([
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'email' => $user->email,
                    'password' => Hash::make('strepen'),
                    'balance' => 0
                ]);
                $oldUserIds[$user->id] = $userModel->id;
            }
            echo "\033[F" . ($index + 1) . ' / ' . $total . ' = ' . round(($index + 1) / $total * 100, 2) . "%\n";
        }
        echo "Importing users done!\n";

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
            $post = json_decode('{' . str_replace("\n", '', $postJson) . '}');
            if ($post->title == '') {
                $post->title = 'Untitled post';
            }
            if ($post->body == '') {
                continue;
            }
            $postModel = new Post();
            $postModel->user_id = 1;
            $postModel->title = $post->title;
            $postModel->body = str_replace('<br />', "\n\n", base64_decode($post->body));
            $postModel->created_at = $post->created_at;
            $postModel->save();
            echo "\033[F" . ($index + 1) . ' / ' . $total . ' = ' . round(($index + 1) / $total * 100, 2) . "%\n";
        }
        echo "Importing posts done!\n";

        // Get all the product information
        echo "Importing all products...\n\n";
        $data = file_get_contents($url . '/bonnen/index.php?id=15&newsId=' . urlencode('0 UNION SELECT \'\', \'\', id, CONCAT(\'{"name":"\', omschrijving, \'","price":\', prijs, \'}\') FROM product'), false, stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ]));
        preg_match_all('/<p>\{([^\}]+)/m', $data, $productsJson);
        $total = count($productsJson[1]);
        foreach ($productsJson[1] as $index => $productJson) {
            $product = json_decode('{' . $productJson . '}');
            Product::create([
                'name' => $product->name,
                'price' => $product->price
            ]);
            echo "\033[F" . ($index + 1) . ' / ' . $total . ' = ' . round(($index + 1) / $total * 100, 2) . "%\n";
        }
        echo "Importing products done!\n";

        // Get all inventory information
        echo "Importing all inventories...\n\n";
        $data = file_get_contents($url . '/bonnen/index.php?id=15&newsId=' . urlencode('0 UNION SELECT \'\', \'\', \'\', CONCAT(\'{"product_id":\', product_id, \',"amount":\', aantal, \',"action":"\', actie, \'","created_at":"\', datum, \'"}\') FROM inkoop'), false, stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ]));
        preg_match_all('/<p>\{([^\}]+)/m', $data, $inventoriesJson);
        $total = count($inventoriesJson[1]);
        foreach ($inventoriesJson[1] as $index => $inventoryJson) {
            $inventory = json_decode('{' . $inventoryJson . '}');
            $product = Product::find($inventory->product_id);
            if ($inventory->amount > 0) {
                $inventoryModel = new Inventory();
                $inventoryModel->user_id = 1;
                $inventoryModel->name = 'Imported inventory on ' . $inventory->created_at;
                $inventoryModel->price = $product->price * $inventory->amount;
                $inventoryModel->created_at = $inventory->created_at;
                $inventoryModel->save();

                $inventoryModel->products()->attach($inventory->product_id, [ 'amount' => $inventory->amount ]);
                $product->amount += $inventory->amount;
                $product->save();
            }
            if ($inventory->amount < 0) {
                $inventory->amount = -$inventory->amount;
                $transaction = new Transaction();
                $transaction->user_id = 1;
                $transaction->type = Transaction::TYPE_TRANSACTION;
                $transaction->name = 'Imported transaction on ' . $inventory->created_at;
                $transaction->price = $product->price * ($inventory->amount);
                $transaction->created_at = $inventory->created_at;
                $transaction->save();

                $transaction->products()->attach($inventory->product_id, [ 'amount' => $inventory->amount ]);
                $product->amount -= $inventory->amount;
                $product->save();
            }
            echo "\033[F" . ($index + 1) . ' / ' . $total . ' = ' . round(($index + 1) / $total * 100, 2) . "% | " . $inventory->created_at . "\n";
        }
        echo "Importing inventories done!\n";

        // Get all transactions information
        echo "Importing all transactions...\n\n";
        $data = file_get_contents($url . '/bonnen/index.php?id=15&newsId=' . urlencode('0 UNION SELECT \'\', \'\', \'\', CONCAT(\'{"old_user_id":\', lid_id, \',"product_id":\', product_id, \',"amount":\', aantal, \',"price":"\', prijs, \'","created_at":"\', datum, \'"}\') FROM schuld'), false, stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ]));
        preg_match_all('/<p>\{([^\}]+)/m', $data, $transactionsJson);
        $total = count($transactionsJson[1]);
        foreach ($transactionsJson[1] as $index => $transactionJson) {
            $transaction = json_decode('{' . $transactionJson . '}');
            if (isset($oldUserIds[$transaction->old_user_id])) {
                $user = User::find($oldUserIds[$transaction->old_user_id]);
                if ($transaction->price != 0) {
                    if ($transaction->product_id == 7) {
                        $transaction->price = -$transaction->price;
                        $transactionModel = new Transaction();
                        $transactionModel->user_id = $user->id;
                        $transactionModel->type = Transaction::TYPE_DEPOSIT;
                        $transactionModel->name = 'Imported deposit on ' . $transaction->created_at;
                        $transactionModel->price = $transaction->price;
                        $transactionModel->created_at = $transaction->created_at;
                        $transactionModel->save();

                        $user->balance += $transaction->price;
                        $user->save();
                    } else {
                        $transactionModel = new Transaction();
                        $transactionModel->user_id = $user->id;
                        $transactionModel->type = Transaction::TYPE_TRANSACTION;
                        $transactionModel->name = 'Imported transaction on ' . $transaction->created_at;
                        $transactionModel->price = $transaction->price;
                        $transactionModel->created_at = $transaction->created_at;
                        $transactionModel->save();

                        $transactionModel->products()->attach($transaction->product_id, [ 'amount' => $transaction->amount ]);
                        $product = Product::find($transaction->product_id);
                        $product->amount -= $transaction->amount;
                        $product->save();

                        $user->balance -= $transaction->price;
                        $user->save();
                    }
                }
            }
            echo "\033[F" . ($index + 1) . ' / ' . $total . ' = ' . round(($index + 1) / $total * 100, 2) . "% | " . $transaction->created_at . "\n";
        }
        echo "Importing transactions done!\n";
    }
}
