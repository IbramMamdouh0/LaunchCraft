<?php
require_once '/app/vendor/autoload.php';
$app = require_once '/app/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\PersonalAccessToken;

// Clean up
PersonalAccessToken::truncate();
User::truncate();

$user = User::factory()->create();
echo "User ID: " . $user->id . PHP_EOL;

$tokenResult = $user->createToken('auth-token');
$plainTextToken = $tokenResult->plainTextToken;
echo "Plain text token: " . $plainTextToken . PHP_EOL;
echo "Token model ID: " . $tokenResult->accessToken->id . PHP_EOL;

// Simulate the HTTP request - extract token from Authorization header
$header = "Bearer " . $plainTextToken;
$bearerToken = str_replace('Bearer ', '', $header);
echo "Bearer token: " . substr($bearerToken, 0, 30) . "..." . PHP_EOL;

// This is what Sanctum does internally
$found = PersonalAccessToken::findToken($bearerToken);
echo "Found by findToken: " . ($found ? "YES" : "NO") . PHP_EOL;

if ($found) {
    echo "Token user_id: " . $found->tokenable_id . PHP_EOL;
    echo "Request user matches: " . ((string)$found->tokenable_id === (string)$user->id ? "YES" : "NO - user=$user->id, tokenable=$found->tokenable_id") . PHP_EOL;
    
    // Try to delete via the token
    $found->delete();
    echo "Deleted. Remaining tokens for user: " . $user->tokens()->count() . PHP_EOL;
}

// Final check
echo "Final token count for user: " . $user->tokens()->count() . PHP_EOL;
