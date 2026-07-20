<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

try {
    $_SERVER['HTTP_ACCEPT'] = 'application/json';

    $tmpDirs = [
        '/tmp/storage/framework/views',
        '/tmp/storage/framework/sessions',
        '/tmp/storage/framework/cache',
        '/tmp/storage/logs',
        '/tmp/bootstrap/cache'
    ];

    foreach ($tmpDirs as $dir) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
    }

    $_ENV['APP_STORAGE'] = '/tmp/storage';
    $_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
    $_ENV['LOG_CHANNEL'] = $_ENV['LOG_CHANNEL'] ?? 'stderr';
    $_ENV['SESSION_DRIVER'] = $_ENV['SESSION_DRIVER'] ?? 'cookie';
    $_ENV['CACHE_STORE'] = $_ENV['CACHE_STORE'] ?? 'array';

    require __DIR__ . '/../vendor/autoload.php';

    /** @var \Illuminate\Foundation\Application $app */
    $app = require __DIR__ . '/../bootstrap/app.php';

    $app->useStoragePath('/tmp/storage');
    $app->useBootstrapPath('/tmp/bootstrap');

    $app->register(\Illuminate\Filesystem\FilesystemServiceProvider::class);
    $app->register(\Illuminate\View\ViewServiceProvider::class);

    $request = \Illuminate\Http\Request::capture();
    $response = $app->handleRequest($request);

    if ($response instanceof \Symfony\Component\HttpFoundation\Response) {
        $response->send();
    }

} catch (\Throwable $e) {
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: application/json');
    }
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ], JSON_PRETTY_PRINT);
    exit;
}
