<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/includes/bootstrap.php';

require_admin();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

if (!csrf_validate($_POST['csrf'] ?? null)) {
    http_response_code(403);
    echo json_encode(['error' => 'CSRF error']);
    exit;
}

$file = $_FILES['file'] ?? null;
if (!is_array($file)) {
    http_response_code(400);
    echo json_encode(['error' => 'No file']);
    exit;
}

if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
    $code = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
    $msg = match ($code) {
        UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'File is too large for current PHP upload limits',
        UPLOAD_ERR_PARTIAL => 'File upload was interrupted (partial upload)',
        UPLOAD_ERR_NO_FILE => 'No file selected',
        UPLOAD_ERR_NO_TMP_DIR => 'Server tmp directory is missing',
        UPLOAD_ERR_CANT_WRITE => 'Server cannot write uploaded file',
        UPLOAD_ERR_EXTENSION => 'Upload blocked by server extension',
        default => 'Unknown upload error',
    };
    http_response_code(422);
    echo json_encode([
        'error' => $msg,
        'code' => $code,
        'upload_max_filesize' => (string) ini_get('upload_max_filesize'),
        'post_max_size' => (string) ini_get('post_max_size'),
    ]);
    exit;
}

$tmpName = (string) ($file['tmp_name'] ?? '');
if ($tmpName === '' || !is_uploaded_file($tmpName)) {
    http_response_code(422);
    echo json_encode(['error' => 'Temporary upload file is missing']);
    exit;
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($tmpName) ?: '';
$allowed = xr_media_allowed_mimes();
if (!isset($allowed[$mime])) {
    http_response_code(422);
    echo json_encode([
        'error' => 'Unsupported file type: ' . $mime,
        'allowed' => array_keys($allowed),
    ]);
    exit;
}

$row = xr_media_save_upload($file);
if ($row === null) {
    http_response_code(422);
    echo json_encode([
        'error' => 'Upload failed (database/storage)',
        'upload_max_filesize' => (string) ini_get('upload_max_filesize'),
        'post_max_size' => (string) ini_get('post_max_size'),
    ]);
    exit;
}

echo json_encode(['url' => (string) ($row['public_path'] ?? '')]);
exit;
