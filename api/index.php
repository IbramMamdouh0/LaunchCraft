<?php

$tmpDirs = ['/tmp/views', '/tmp/sessions', '/tmp/cache', '/tmp/logs'];
foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

$_ENV['VIEW_COMPILED_PATH'] = '/tmp/views';

require __DIR__ . '/../public/index.php';
