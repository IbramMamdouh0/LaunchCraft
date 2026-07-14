<?php
require __DIR__ . '/../../vendor/autoload.php';

$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::factory()->create();
echo "User created: " . $user->id . "\n";
echo "User connection: " . $user->getConnectionName() . "\n";

$patModel = new App\Models\PersonalAccessToken();
echo "PAT connection: " . $patModel->getConnectionName() . "\n";

$token = $user->createToken('test');
echo "Token created: " . $token->plainTextToken . "\n";
echo "Success!\n";
