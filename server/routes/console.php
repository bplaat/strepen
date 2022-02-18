<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('lint', function () {
    system('./vendor/bin/php-cs-fixer fix .');
})->purpose('Lint and fix code to use PSR12');

Artisan::command('prod', function () {
    Artisan::call('optimize');
    Artisan::call('view:cache');
})->purpose('Cache stuff for production');
