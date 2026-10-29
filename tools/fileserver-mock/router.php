<?php

// Intentionally standalone: no Laravel bootstrap, database, or encryption key.
if (PHP_SAPI !== 'cli-server' || ! in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)) {
    http_response_code(404);
    exit;
}

require_once dirname(__DIR__, 2).'/app/Support/MockFileserverStorage.php';

use App\Support\MockFileserverStorage;

function respond(int $status, array $body): never
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($body);
    exit;
}

header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

try {
    $root = getenv('BURIAL_MOCK_ROOT');
    $token = getenv('BURIAL_MOCK_TOKEN');
    $policy = getenv('BURIAL_MOCK_DUPLICATES');
    $maxBytes = filter_var(getenv('BURIAL_MOCK_MAX_BYTES') ?: 32 * 1024 * 1024, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 32 * 1024 * 1024]]);
    if (! $root || ! $token || $maxBytes === false || ! in_array($policy, ['reject', 'overwrite'], true)) {
        respond(500, ['status' => 'error', 'message' => 'Mock server is not configured.']);
    }
    $storage = new MockFileserverStorage($root);
    $path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '');
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method === 'GET' && str_starts_with($path, '/burial/')) {
        $name = substr($path, 8);
        if (! MockFileserverStorage::validName($name)) {
            respond(400, ['status' => 'error', 'message' => 'Invalid filename.']);
        }
        $bytes = $storage->read($name);
        if ($bytes === null) {
            respond(404, ['status' => 'error', 'message' => 'File not found.']);
        }
        header('Content-Type: application/octet-stream');
        header('Content-Length: '.strlen($bytes));
        echo $bytes;
        exit;
    }
    if ($path !== '/upload' || $method !== 'POST') {
        respond(404, ['status' => 'error', 'message' => 'Endpoint not found.']);
    }
    if ((int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 34 * 1024 * 1024) {
        respond(413, ['status' => 'error', 'message' => 'Upload too large.']);
    }
    if (! is_string($_POST['token'] ?? null) || ! hash_equals($token, $_POST['token'])) {
        respond(401, ['status' => 'error', 'message' => 'Invalid token.']);
    }
    $file = $_FILES['file'] ?? null;
    if (! is_array($file) || ! is_int($file['error'] ?? null)) {
        respond(400, ['status' => 'error', 'message' => 'One file is required.']);
    }
    if (in_array($file['error'], [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true) || ($file['size'] ?? 0) > $maxBytes) {
        respond(413, ['status' => 'error', 'message' => 'Upload too large.']);
    }
    if ($file['error'] !== UPLOAD_ERR_OK || ! is_uploaded_file($file['tmp_name']) || ! MockFileserverStorage::validName($file['name'])) {
        respond(400, ['status' => 'error', 'message' => 'Invalid upload.']);
    }
    $storage->cleanup();
    $bytes = file_get_contents($file['tmp_name']);
    if ($bytes === false) {
        throw new RuntimeException('Cannot read upload.');
    }
    if (! $storage->write($file['name'], $bytes, $policy === 'overwrite')) {
        respond(409, ['status' => 'error', 'message' => 'File already exists.']);
    }
    respond(201, ['status' => 'success', 'filename' => $file['name']]);
} catch (Throwable) {
    respond(500, ['status' => 'error', 'message' => 'Mock storage failed.']);
}
