<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/includes/bootstrap.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/media.php', true, 302);
    exit;
}

if (!csrf_validate($_POST['csrf'] ?? null)) {
    http_response_code(403);
    $_SESSION['admin_media_flash'] = ['i18n' => 'flash.media_csrf', 'vars' => []];
    header('Location: /admin/media.php', true, 302);
    exit;
}

$file = $_FILES['file'] ?? null;
if (!is_array($file)) {
    $_SESSION['admin_media_flash'] = ['i18n' => 'flash.media_no_file', 'vars' => []];
    header('Location: /admin/media.php', true, 302);
    exit;
}

$row = xr_media_save_upload($file);
if ($row === null) {
    $_SESSION['admin_media_flash'] = ['i18n' => 'flash.media_fail', 'vars' => []];
    header('Location: /admin/media.php', true, 302);
    exit;
}

$path = (string) ($row['public_path'] ?? '');
$_SESSION['admin_media_flash'] = ['i18n' => 'flash.media_ok', 'vars' => ['path' => $path]];
header('Location: /admin/media.php', true, 302);
exit;
