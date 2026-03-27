<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/blocks.php';

$site = load_site();
$xr_page_key = 'professionals';
$xr_page_meta = $site['professionals']['meta'];
$nav = xr_resolved_nav($site, 'professionals');

require dirname(__DIR__) . '/includes/views/header.php';
require dirname(__DIR__) . '/includes/views/chrome_header.php';
?>
<main id="main" role="main">
    <?php xr_render_blocks($site['professionals']['blocks']); ?>
</main>

<footer class="site-footer">
    <p>© <?= date('Y') ?> <?= h((string) ($site['professionals']['meta']['title'] ?? '')) ?> · <a href="/">Home</a> · <a href="/institutions.php">Institutions</a> · <a href="/blog.php">Blog</a> · <a href="/partners.php">Partners</a> · <a href="/admin/">Admin</a></p>
</footer>

<?php require dirname(__DIR__) . '/includes/views/footer.php'; ?>
