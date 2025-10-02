<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('lint', function () {
    system('./vendor/bin/pint --preset psr12 --test');
})->purpose('Lint code');

Artisan::command('prod', function () {
    Artisan::call('optimize');
    Artisan::call('view:cache');
})->purpose('Cache stuff for production');
