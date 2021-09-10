<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\Product;
use App\Models\User;
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
        // Artisan::call('migrate:fresh --seed');

        // Get all the user information
        $data = file_get_contents($url . '/bonnen/index.php?id=15&newsId=' . urlencode('0 UNION SELECT \'\', \'\', \'\', CONCAT(\'{"id":\', id, \',"name":"\', naam, \'","email":"\', email, \'","active":\', active, \'}\') FROM stamleden'), false, stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ]));
        preg_match_all('/<p>\{([^\}]+)/m', $data, $usersJson);
        foreach ($usersJson[1] as $userJson) {
            $user = json_decode('{' . $userJson . '}');
            $nameParts = explode(' ', $user->name);
            $firstname = $nameParts[0];
            array_shift($nameParts);
            $lastname = implode(' ', $nameParts);
            if ($user->active && $lastname != 'van der Plaat') {
                User::create([
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'email' => $user->email,
                    'password' => Hash::make('strepen'),
                    'role' => User::ROLE_NORMAL,
                    'balance' => 0
                ]);
            }
        }

        // Get all the posts information
        $data = file_get_contents($url . '/bonnen/index.php?id=15&newsId=' . urlencode('0 UNION SELECT \'\', \'\', id, CONCAT(\'{"title":"\', onderwerp, \'","body":"\', TO_BASE64(bericht), \'","created_at":"\', datum, \'"}\') FROM nieuws'), false, stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ]));
        preg_match_all('/<p>\{([^\}]+)/m', $data, $postsJson);
        foreach ($postsJson[1] as $postJson) {
            $post = json_decode('{' . str_replace("\n", '', $postJson) . '}');
            if ($post->title == '') {
                $post->title = 'Untitled post';
            }
            if ($post->body == '') {
                continue;
            }
            $postModel = Post::create([
                'user_id' => 1,
                'title' => $post->title,
                'body' => str_replace('<br />', "\n\n", base64_decode($post->body))
            ]);
            $postModel->created_at = $post->created_at;
            $postModel->save();
        }

        // Get all the product information
        $data = file_get_contents($url . '/bonnen/index.php?id=15&newsId=' . urlencode('0 UNION SELECT \'\', \'\', id, CONCAT(\'{"name":"\', omschrijving, \'","price":\', prijs, \'}\') FROM product'), false, stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ]));
        preg_match_all('/<p>\{([^\}]+)/m', $data, $productsJson);
        foreach ($productsJson[1] as $productJson) {
            $product = json_decode('{' . $productJson . '}');
            Product::create([
                'name' => $product->name,
                'price' => $product->price,
                'amount' => 0
            ]);
        }
    }
}
