<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/includes/bootstrap.php';

require_admin();

$flash = $_SESSION['admin_media_flash'] ?? '';
unset($_SESSION['admin_media_flash']);
$flashText = admin_render_flash($flash);

$list = xr_media_list(300);
$token = csrf_token();
$dbOk = xr_site_uses_database() && is_readable(xr_site_db_path());
$uiLang = admin_lang();

function xr_fmt_bytes(int $n): string
{
    if ($n < 1024) {
        return (string) $n . ' B';
    }
    if ($n < 1048576) {
        return round($n / 1024, 1) . ' KB';
    }

    return round($n / 1048576, 2) . ' MB';
}

?>
<!DOCTYPE html>
<html lang="<?= h($uiLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h(admin_t('page.media_title')) ?></title>
    <link rel="stylesheet" href="/admin/admin.css">
</head>
<body class="admin-body admin-body--full">
<header class="admin-top admin-top--split">
    <div class="admin-top__lead">
        <strong class="admin-top__title"><?= h(admin_t('media.heading')) ?></strong>
        <div class="admin-lang" role="group" aria-label="<?= h(admin_t('lang.label')) ?>">
            <a class="admin-lang__link<?= $uiLang === 'ru' ? ' is-active' : '' ?>" href="<?= h(admin_lang_switch_url('ru')) ?>"><?= h(admin_t('lang.ru')) ?></a>
            <span class="admin-lang__sep" aria-hidden="true">·</span>
            <a class="admin-lang__link<?= $uiLang === 'en' ? ' is-active' : '' ?>" href="<?= h(admin_lang_switch_url('en')) ?>"><?= h(admin_t('lang.en')) ?></a>
        </div>
    </div>
    <span class="admin-top__links">
        <a href="/admin/"><?= h(admin_t('nav.content')) ?></a>
        <a href="/" target="_blank" rel="noopener"><?= h(admin_t('nav.site')) ?></a>
        <a href="/admin/logout.php"><?= h(admin_t('nav.logout')) ?></a>
    </span>
</header>

<?php if ($flashText !== ''): ?>
    <p class="admin-flash"><?= h($flashText) ?></p>
<?php endif; ?>

<?php if (!$dbOk): ?>
    <p class="admin-hint"><?= h(admin_t('media.no_db')) ?></p>
<?php else: ?>
    <form class="admin-form" method="post" action="/admin/media_upload.php" enctype="multipart/form-data" style="margin-bottom: 32px;">
        <input type="hidden" name="csrf" value="<?= h($token) ?>">
        <fieldset class="admin-fieldset">
            <legend><?= h(admin_t('media.upload_legend')) ?></legend>
            <p class="admin-hint"><?= h(admin_t('media.upload_hint')) ?></p>
            <label class="admin-label"><?= h(admin_t('media.file')) ?></label>
            <input class="admin-input" type="file" name="file" accept="image/jpeg,image/png,image/webp,image/gif,video/mp4,video/webm" required>
            <button class="admin-btn admin-btn--primary" type="submit" style="margin-top:16px;"><?= h(admin_t('btn.upload')) ?></button>
        </fieldset>
    </form>

    <fieldset class="admin-fieldset">
        <legend><?= h(admin_t('media.list_legend')) ?></legend>
        <?php if ($list === []): ?>
            <p class="admin-hint"><?= h(admin_t('media.empty')) ?></p>
        <?php else: ?>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                    <tr>
                        <th><?= h(admin_t('media.th_id')) ?></th>
                        <th><?= h(admin_t('media.th_preview')) ?></th>
                        <th><?= h(admin_t('media.th_url')) ?></th>
                        <th><?= h(admin_t('media.th_size')) ?></th>
                        <th><?= h(admin_t('media.th_name')) ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($list as $row): ?>
                        <?php
                        $path = (string) ($row['public_path'] ?? '');
                        $mime = (string) ($row['mime'] ?? '');
                        $isImg = str_starts_with($mime, 'image/');
                        ?>
                        <tr>
                            <td><?= (int) ($row['id'] ?? 0) ?></td>
                            <td>
                                <?php if ($isImg && $path !== ''): ?>
                                    <img src="<?= h($path) ?>" alt="" class="admin-table__thumb">
                                <?php else: ?>
                                    <?= h($mime) ?>
                                <?php endif; ?>
                            </td>
                            <td><code class="admin-code-select"><?= h($path) ?></code></td>
                            <td><?= h(xr_fmt_bytes((int) ($row['size_bytes'] ?? 0))) ?></td>
                            <td><?= h((string) ($row['original_name'] ?? '')) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </fieldset>
<?php endif; ?>
</body>
</html>
