<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/includes/bootstrap.php';
require_once dirname(__DIR__, 2) . '/includes/admin_save.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/', true, 302);
    exit;
}

if (!csrf_validate($_POST['csrf'] ?? null)) {
    http_response_code(403);
    echo 'CSRF';
    exit;
}

$merged = merge_site_from_post(load_site());
if (save_site($merged)) {
    $_SESSION['admin_flash'] = [
        'i18n' => xr_site_uses_database() && is_readable(xr_site_db_path())
            ? 'flash.saved_sqlite'
            : 'flash.saved_json',
        'vars' => [],
    ];
} else {
    $_SESSION['admin_flash'] = ['i18n' => 'flash.save_error', 'vars' => []];
}

header('Location: /admin/', true, 302);
