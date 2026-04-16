<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/blocks.php';

$site = load_site();
$xr_page_key = 'professionals';
$xr_page_meta = $site['professionals']['meta'];
$nav = xr_resolved_nav($site, 'professionals');
$proBlocks = is_array($site['professionals']['blocks'] ?? null) ? $site['professionals']['blocks'] : [];

// Ensure preorder section exists right after p-2-17 even if DB still has older block set.
$hasP218 = false;
foreach ($proBlocks as $blk) {
    if (is_array($blk) && (($blk['id'] ?? '') === 'p-2-18')) {
        $hasP218 = true;
        break;
    }
}
if (!$hasP218) {
    $insertAt = count($proBlocks);
    foreach ($proBlocks as $i => $blk) {
        if (is_array($blk) && (($blk['id'] ?? '') === 'p-2-17')) {
            $insertAt = $i + 1;
            break;
        }
    }
    $newBlock = [
        'type' => 'preorder_banner',
        'id' => 'p-2-18',
        'props' => [
            'title' => "Leap into Your Next-GeN\nMedical Excellence",
            'subtitle' => 'Pre-Order Now - Reserve Your Access',
            'note' => 'Get 3 Years Bonus - 1 Free Month Each Year',
            'button_label' => 'Pre-Order Now',
            'href' => '#hubspot-demo',
        ],
    ];
    array_splice($proBlocks, $insertAt, 0, [$newBlock]);
}

require dirname(__DIR__) . '/includes/views/header.php';
require dirname(__DIR__) . '/includes/views/chrome_header.php';
?>
<main id="main" role="main">
    <?php xr_render_blocks($proBlocks); ?>
</main>

<?php require dirname(__DIR__) . '/includes/views/footer.php'; ?>
