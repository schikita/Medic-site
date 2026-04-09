<?php
/** @var array $site */
/** @var array $xr_page_meta @deprecated use $site[$page]['meta'] after load_site */
$xr_page_key = $xr_page_key ?? 'home';
$lang = 'en';
if (is_array($site['seo'] ?? null)) {
    $lang = trim((string) ($site['seo']['language'] ?? 'en')) ?: 'en';
}
?>
<!DOCTYPE html>
<html lang="<?= h($lang) ?>">
<head>
<?php
    xr_render_seo_head($site, $xr_page_key);
?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <?php
    $xrCssV = static function (string $relPath): int {
        $fs = ROOT . '/public' . $relPath;
        $m = @filemtime($fs);

        return is_int($m) && $m > 0 ? $m : time();
    };
    ?>
    <link rel="stylesheet" href="/assets/css/main.css?v=<?= (int) $xrCssV('/assets/css/main.css') ?>">
    <link rel="stylesheet" href="/assets/css/blocks.css?v=<?= (int) $xrCssV('/assets/css/blocks.css') ?>">
</head>
<body class="xr-site">
<a class="skip-link" href="#main">Skip to content</a>
