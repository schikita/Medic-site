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

$row = xr_media_save_upload($file);
if ($row === null) {
    http_response_code(422);
    echo json_encode(['error' => 'Upload failed (check type/size/database)']);
    exit;
}

echo json_encode(['url' => (string) ($row['public_path'] ?? '')]);
exit;
