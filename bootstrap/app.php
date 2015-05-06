<?php

use Symfony\Component\Finder\Finder;

require __DIR__.'/../vendor/autoload.php';
$app = new \Bkoetsier\BaseConsole\Foundation\Container(realpath(__DIR__.'/../'));
$app->singleton(
    'Illuminate\Contracts\Console\Kernel',
    'Bkoetsier\BaseConsole\Foundation\Kernel'
);
$providers = require_once(base_path('providers.php'));
foreach($providers as $provider)
{
    $app->register($provider);
}

return $app;