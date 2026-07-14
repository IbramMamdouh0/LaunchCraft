<?php
require_once '/app/vendor/autoload.php';
$app = require_once '/app/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
echo "PAT model: " . Laravel\Sanctum\Sanctum::$personalAccessTokenModel . PHP_EOL;
$m = new App\Models\PersonalAccessToken;
echo "getKeyType: " . $m->getKeyType() . PHP_EOL;
echo "class: " . get_class($m) . PHP_EOL;
