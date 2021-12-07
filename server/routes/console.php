<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

// Aliases
Artisan::command('lint', function () {
    chdir('vendor/bin');
    system('php-cs-fixer fix .');
})->purpose('Lint and fix code to use PSR12');

Artisan::command('prod', function () {
    Artisan::call('optimize');
    Artisan::call('view:cache');
})->purpose('Cache stuff for production');

// Stupid stuff
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
