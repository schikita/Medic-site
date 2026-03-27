<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/includes/bootstrap.php';

$lang = (string) ($_GET['lang'] ?? '');
if ($lang === 'ru' || $lang === 'en') {
    admin_set_lang($lang);
}

$return = admin_safe_return(isset($_GET['return']) ? (string) $_GET['return'] : null);
header('Location: ' . $return, true, 302);
exit;
