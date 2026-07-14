<?php
require_once '/app/vendor/autoload.php';
$app = require_once '/app/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\PersonalAccessToken;

// Clean
PersonalAccessToken::truncate();
User::truncate();

$user = User::factory()->create();
echo "User: " . $user->id . PHP_EOL;
$tokenResult = $user->createToken('auth-token');
$plainText = $tokenResult->plainTextToken;
echo "Token: $plainText" . PHP_EOL;

// Test findToken
$found = PersonalAccessToken::findToken($plainText);
echo "findToken: " . ($found ? 'YES' : 'NO') . PHP_EOL;

// Test Sanctum's Guard isValidBearerToken
$model = new PersonalAccessToken;
echo "getKeyType: " . $model->getKeyType() . PHP_EOL;

// Simulate what Guard does
$request = Illuminate\Http\Request::create('/api/user', 'GET');
$request->headers->set('Authorization', "Bearer $plainText");

echo "Bearer: " . $request->bearerToken() . PHP_EOL;

$token2 = $request->bearerToken();
echo "str_contains |: " . (str_contains($token2, '|') ? 'yes' : 'no') . PHP_EOL;

if (!is_null($token2) && str_contains($token2, '|')) {
    $m = new PersonalAccessToken;
    $keyType = $m->getKeyType();
    echo "Key type: $keyType" . PHP_EOL;
    if ($keyType === 'int') {
        [$id, $t] = explode('|', $token2, 2);
        echo "ID: $id, digit: " . (ctype_digit($id) ? 'yes' : 'no') . PHP_EOL;
        echo "Valid: " . (ctype_digit($id) && !empty($t) ? 'true' : 'false') . PHP_EOL;
    } else {
        echo "Key type is not int, falls through" . PHP_EOL;
    }
    echo "Final: " . (!empty($token2) ? 'true' : 'false') . PHP_EOL;
}
