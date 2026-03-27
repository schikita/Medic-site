<?php
/** @var array $nav */
/** @var string $top_id */
$top_id = $top_id ?? 'top';
?>
<header class="site-header site-header--glass" id="<?= h($top_id) ?>">
    <div class="site-header__inner">
        <button type="button" class="nav-toggle" aria-expanded="false" aria-controls="primary-nav" aria-label="Open menu">
            <span class="nav-toggle__bar"></span>
            <span class="nav-toggle__bar"></span>
            <span class="nav-toggle__bar"></span>
        </button>
        <a class="site-logo" href="/">
            <img src="/assets/img/logo.png" alt="<?= h($nav['logo_alt']) ?>" width="43" height="32">
        </a>
        <nav class="site-nav" id="primary-nav" aria-label="Primary">
            <ul class="site-nav__links">
                <?php foreach ($nav['items'] as $item): ?>
                    <?php
                    $active = !empty($item['active']);
                    $cls = 'site-nav__link' . ($active ? ' is-active' : '');
                    ?>
                    <li class="site-nav__item">
                        <a class="<?= h($cls) ?>" href="<?= h((string) ($item['href'] ?? '#')) ?>"<?= $active ? ' aria-current="page"' : '' ?>><?= h((string) ($item['label'] ?? '')) ?></a>
                        <?php if ($active): ?>
                            <span class="site-nav__underline" aria-hidden="true">
                                <img src="/assets/img/nav-underline.svg" alt="">
                            </span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="site-nav__cta">
                <a class="btn btn--outline js-hubspot-trigger" href="<?= h((string) ($nav['cta_outline']['href'] ?? '#')) ?>" data-hubspot-kind="whitepaper"><?= h((string) ($nav['cta_outline']['label'] ?? '')) ?></a>
                <a class="btn btn--gradient js-hubspot-trigger" href="<?= h((string) ($nav['cta_gradient']['href'] ?? '#')) ?>" data-hubspot-kind="demo"><?= h((string) ($nav['cta_gradient']['label'] ?? '')) ?></a>
            </div>
        </nav>
    </div>
    <div class="site-header__accent" aria-hidden="true"></div>
</header>
