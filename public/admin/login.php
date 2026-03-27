<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/includes/bootstrap.php';

$cfg = admin_config();
$errorKey = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pass = (string) ($_POST['password'] ?? '');
    $hash = (string) ($cfg['password_hash'] ?? '');
    if ($hash === '') {
        $errorKey = 'login.err_no_hash';
    } elseif (password_verify($pass, $hash)) {
        $_SESSION['admin_ok'] = true;
        header('Location: /admin/', true, 302);
        exit;
    } else {
        $errorKey = 'login.err_bad_password';
    }
}

if (admin_logged_in()) {
    header('Location: /admin/', true, 302);
    exit;
}

$uiLang = admin_lang();
$errorText = $errorKey !== '' ? admin_t($errorKey) : '';
?>
<!DOCTYPE html>
<html lang="<?= h($uiLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h(admin_t('page.login_title')) ?></title>
    <link rel="stylesheet" href="/admin/admin.css">
</head>
<body class="admin-body">
<div class="admin-card">
    <div class="admin-card__head">
        <h1 class="admin-card__title"><?= h(admin_t('login.heading')) ?></h1>
        <div class="admin-lang admin-lang--compact" role="group" aria-label="<?= h(admin_t('lang.label')) ?>">
            <a class="admin-lang__link<?= $uiLang === 'ru' ? ' is-active' : '' ?>" href="<?= h(admin_lang_switch_url('ru')) ?>"><?= h(admin_t('lang.ru')) ?></a>
            <span class="admin-lang__sep" aria-hidden="true">·</span>
            <a class="admin-lang__link<?= $uiLang === 'en' ? ' is-active' : '' ?>" href="<?= h(admin_lang_switch_url('en')) ?>"><?= h(admin_t('lang.en')) ?></a>
        </div>
    </div>
    <?php if ($errorText !== ''): ?>
        <p class="admin-error"><?= h($errorText) ?></p>
    <?php endif; ?>
    <?php if (($cfg['password_hash'] ?? '') === ''): ?>
        <p class="admin-hint"><?= h(admin_t('login.hint_config')) ?></p>
        <pre class="admin-code"><?= h(admin_t('login.code_hint')) ?></pre>
    <?php endif; ?>
    <form method="post" action="">
        <label class="admin-label" for="password"><?= h(admin_t('login.password')) ?></label>
        <input class="admin-input" type="password" id="password" name="password" required autocomplete="current-password">
        <button class="admin-btn" type="submit"><?= h(admin_t('btn.login')) ?></button>
    </form>
    <p class="admin-foot"><a href="/"><?= h(admin_t('login.to_site')) ?></a></p>
</div>
</body>
</html>
