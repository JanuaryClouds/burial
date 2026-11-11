<?php

// Standalone, loopback only. Never boot Laravel or read production credentials.
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
    $secret = getenv('BURIAL_MOCK_TOKEN');
    $policy = getenv('BURIAL_MOCK_DUPLICATES');
    $maxBytes = filter_var(getenv('BURIAL_MOCK_MAX_BYTES') ?: 30 * 1024 * 1024, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 30 * 1024 * 1024]]);
    date_default_timezone_set(getenv('BURIAL_MOCK_TIMEZONE') ?: 'Asia/Manila');
    if (! $root || ! $secret || $maxBytes === false || ! in_array($policy, ['reject', 'overwrite'], true)) {
        respond(500, ['status' => 'error', 'message' => 'Mock server is not configured.']);
    }
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $method = $_SERVER['REQUEST_METHOD'];
    if ($path !== '/apiburial/' || ! in_array($method, ['GET', 'POST'], true)) {
        respond(404, ['status' => 'error', 'message' => 'Endpoint not found.']);
    }
    // Allow Base64 expansion, but bound parsing before reading the body.
    if ((int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 42 * 1024 * 1024) {
        respond(400, ['status' => 'error', 'message' => 'Upload too large.']);
    }
    $params = $method === 'GET' ? $_GET : $_POST;
    if ($method === 'POST' && str_starts_with(strtolower($_SERVER['CONTENT_TYPE'] ?? ''), 'application/json')) {
        $params = json_decode(file_get_contents('php://input'), true);
        if (! is_array($params)) {
            respond(400, ['status' => 'error', 'message' => 'Invalid JSON.']);
        }
    }
    $token = $params['token'] ?? null;
    if (! is_string($token) || $token === '') {
        respond(401, ['status' => 'error', 'message' => 'Token required.']);
    }
    if (! hash_equals(date('Ymd').$secret, $token)) {
        respond(403, ['status' => 'error', 'message' => 'Invalid or expired token.']);
    }
    $action = $params['action'] ?? null;
    $allowed = $method === 'GET' ? ['view', 'download', 'list'] : ['upload', 'delete'];
    if (! is_string($action) || ! in_array($action, $allowed, true)) {
        respond(400, ['status' => 'error', 'message' => 'Invalid action or method.']);
    }
    $folder = $params['folder'] ?? '';
    if (! is_string($folder) || ($folder !== '' && ! MockFileserverStorage::validName($folder))) {
        respond(400, ['status' => 'error', 'message' => 'Invalid folder.']);
    }
    $rootStorage = new MockFileserverStorage($root);
    $storage = $rootStorage->folder($folder);
    if ($action === 'list') {
        $limit = filter_var($params['limit'] ?? 100, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 1000]]);
        if ($limit === false) {
            respond(400, ['status' => 'error', 'message' => 'Invalid limit.']);
        }
        $all = $storage->files();
        $files = array_slice($all, 0, $limit);
        respond(200, ['status' => 'success', 'folder' => $folder, 'count' => count($files), 'total_count' => count($all), 'files' => $files]);
    }
    if ($action !== 'upload') {
        $name = $params['file'] ?? null;
        if (! is_string($name) || ! MockFileserverStorage::validName($name)) {
            respond(400, ['status' => 'error', 'message' => 'Invalid filename.']);
        }
        if ($action === 'delete') {
            if (! $storage->delete($name)) {
                respond(404, ['status' => 'error', 'message' => 'File not found.']);
            }
            respond(200, ['status' => 'success', 'file' => $name, 'folder' => $folder]);
        }
        $bytes = $storage->read($name);
        if ($bytes === null) {
            respond(404, ['status' => 'error', 'message' => 'File not found.']);
        }
        // Opaque-byte behavior remains deliberate until upstream .enc support is confirmed.
        $mime = (new finfo(FILEINFO_MIME_TYPE))->buffer($bytes);
        header('Content-Type: '.(in_array($mime, ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'], true) ? $mime : 'application/octet-stream'));
        header('Content-Disposition: '.($action === 'download' ? 'attachment' : 'inline').'; filename="'.$name.'"');
        header('Content-Length: '.strlen($bytes));
        echo $bytes;
        exit;
    }
    if (isset($params['file_data'])) {
        $name = $params['file_name'] ?? null;
        $data = $params['file_data'];
        if (! is_string($data)) {
            respond(400, ['status' => 'error', 'message' => 'Invalid Base64.']);
        }
        $data = preg_replace('/\Adata:[^,]*;base64,/', '', $data);
        $bytes = base64_decode($data, true);
        if ($bytes === false) {
            respond(400, ['status' => 'error', 'message' => 'Invalid Base64.']);
        }
    } else {
        $file = $_FILES['file'] ?? null;
        if (! is_array($file) || ($file['error'] ?? null) !== UPLOAD_ERR_OK || ! is_uploaded_file($file['tmp_name'])) {
            respond(400, ['status' => 'error', 'message' => 'Invalid upload.']);
        }
        $name = $file['name'];
        $bytes = file_get_contents($file['tmp_name']);
    }
    if (! is_string($name) || ! MockFileserverStorage::validName($name) || ! is_string($bytes) || strlen($bytes) > $maxBytes) {
        respond(400, ['status' => 'error', 'message' => 'Invalid filename or upload size.']);
    }
    if (! in_array(strtolower(pathinfo($name, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp', 'pdf', 'enc'], true)) {
        respond(400, ['status' => 'error', 'message' => 'Disallowed extension.']);
    }
    $rootStorage->cleanup();
    if (! $storage->write($name, $bytes, $policy === 'overwrite')) {
        respond(409, ['status' => 'error', 'message' => 'File already exists.']);
    }
    respond(200, ['status' => 'success', 'file' => $name, 'folder' => $folder, 'reencoded' => false]);
} catch (Throwable) {
    respond(500, ['status' => 'error', 'message' => 'Mock storage failed.']);
}
