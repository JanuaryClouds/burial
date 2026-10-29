<?php

// Synthetic browser-only fixture. No application bootstrap or database access.
if (PHP_SAPI !== 'cli-server' || ! in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)) {
    http_response_code(404);
    exit;
}
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'nonce-image-viewer-fixture'; script-src-attr 'none'; img-src 'self' blob:; connect-src 'self'; style-src 'self' 'unsafe-inline'; object-src 'none'; base-uri 'none'");
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($path === '/') {
    header('Content-Type: text/html');
    readfile(__DIR__.'/image-viewer.html');
} elseif ($path === '/image-viewer.js') {
    header('Content-Type: text/javascript');
    readfile(dirname(__DIR__, 2).'/public/js/image-viewer.js');
} else {
    http_response_code(404);
}
