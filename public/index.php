<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/blocks.php';

$site = load_site();
$xr_page_key = 'home';
$xr_page_meta = $site['home']['meta'];
$nav = xr_resolved_nav($site, 'home');

require dirname(__DIR__) . '/includes/views/header.php';
require dirname(__DIR__) . '/includes/views/chrome_header.php';
?>
<main id="main" role="main">
    <?php xr_render_blocks($site['home']['blocks']); ?>
</main>

<?php require dirname(__DIR__) . '/includes/views/footer.php'; ?>
