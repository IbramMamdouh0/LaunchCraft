<?php

$_SERVER['HTTP_ACCEPT'] = 'application/json';

$tmpDirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/cache',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

$_ENV['APP_STORAGE'] = '/tmp/storage';
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
$_ENV['LOG_CHANNEL'] = $_ENV['LOG_CHANNEL'] ?? 'stderr';

foreach (glob(__DIR__ . '/../bootstrap/cache/*.php') as $cacheFile) {
    if (basename($cacheFile) !== '.gitignore' && basename($cacheFile) !== '.gitkeep') {
        @unlink($cacheFile);
    }
}

try {
    require __DIR__ . '/../public/index.php';
} catch (Throwable $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'Internal server error',
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
    ]);
}
