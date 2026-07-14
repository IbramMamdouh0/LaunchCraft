<?php
require_once '/app/vendor/autoload.php';
$app = require_once '/app/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "PAT model: " . Laravel\Sanctum\Sanctum::$personalAccessTokenModel . PHP_EOL;

$providers = $app->getLoadedProviders();
foreach ($providers as $class => $loaded) {
    echo "  $class => " . ($loaded ? 'loaded' : 'not loaded') . PHP_EOL;
}
