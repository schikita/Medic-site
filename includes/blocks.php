<?php

declare(strict_types=1);

function xr_render_blocks(array $blocks): void
{
    foreach ($blocks as $i => $block) {
        if (!is_array($block) || empty($block['type'])) {
            continue;
        }
        $type = (string) $block['type'];
        $props = is_array($block['props'] ?? null) ? $block['props'] : [];
        $bidRaw = (string) ($block['id'] ?? ('b-' . $i));
        echo '<section class="xr-block xr-block--' . h($type) . '" id="' . h($bidRaw) . '" data-block-type="' . h($type) . '">';
        $fn = 'xr_block_' . $type;
        if (function_exists($fn)) {
            $fn($props, $bidRaw);
        }
        echo '</section>';
    }
}

function xr_block_hero_fullscreen(array $p, string $blockId = ''): void
{
    $poster = (string) ($p['poster'] ?? '/assets/img/hero-bg.png');
    $mp4 = (string) ($p['video_mp4'] ?? '');
    $webm = (string) ($p['video_webm'] ?? '');
    ?>
    <div class="xr-hero-full">
        <div class="xr-hero-full__media">
            <?php if ($mp4 !== '' || $webm !== ''): ?>
                <video class="xr-hero-full__video" autoplay muted loop playsinline poster="<?= h($poster) ?>">
                    <?php if ($webm !== ''): ?><source src="<?= h($webm) ?>" type="video/webm"><?php endif; ?>
                    <?php if ($mp4 !== ''): ?><source src="<?= h($mp4) ?>" type="video/mp4"><?php endif; ?>
                </video>
            <?php else: ?>
                <img class="xr-hero-full__poster" src="<?= h($poster) ?>" alt="">
            <?php endif; ?>
            <div class="xr-hero-full__shade" aria-hidden="true"></div>
        </div>
        <?php
        $note = trim((string) ($p['overlay_note'] ?? ''));
        $lines = $p['overlay_lines'] ?? [];
        if ($note !== '' || (is_array($lines) && $lines !== [])):
            ?>
            <div class="xr-hero-full__labels">
                <?php if ($note !== ''): ?><p class="xr-hero-full__note"><?= h($note) ?></p><?php endif; ?>
                <?php
                if (is_array($lines)) {
                    foreach ($lines as $line) {
                        $t = trim((string) $line);
                        if ($t !== '') {
                            echo '<p class="xr-hero-full__label">' . h($t) . '</p>';
                        }
                    }
                }
                ?>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

function xr_block_intro_gradient(array $p, string $blockId = ''): void
{
    ?>
    <div class="xr-intro-gradient">
        <h2 class="xr-intro-gradient__headline">
            <span class="xr-intro-gradient__line xr-reveal"><?= h((string) ($p['headline_line1'] ?? '')) ?></span>
            <span class="xr-intro-gradient__line xr-reveal"><?= h((string) ($p['headline_line2'] ?? '')) ?></span>
        </h2>
        <p class="xr-intro-gradient__tagline xr-reveal"><?= h((string) ($p['tagline'] ?? '')) ?></p>
    </div>
    <?php
}

function xr_block_wave_slider(array $p, string $blockId = ''): void
{
    $slides = is_array($p['slides'] ?? null) ? $p['slides'] : [];
    $interval = (int) ($p['interval_ms'] ?? 5000);
    if ($slides === []) {
        return;
    }
    ?>
    <div class="xr-wave-slider" data-carousel data-interval="<?= (int) $interval ?>">
        <div class="xr-wave-slider__wave" aria-hidden="true"></div>
        <div class="xr-wave-slider__viewport">
            <?php foreach ($slides as $i => $s): ?>
                <?php if (!is_array($s)) {
                    continue;
                } ?>
                <figure class="xr-wave-slider__slide<?= $i === 0 ? ' is-active' : '' ?>" data-carousel-slide>
                    <img src="<?= h((string) ($s['image'] ?? '')) ?>" alt="">
                    <figcaption class="xr-wave-slider__caption"><?= h((string) ($s['caption'] ?? '')) ?></figcaption>
                </figure>
            <?php endforeach; ?>
        </div>
        <div class="xr-wave-slider__dots" role="tablist">
            <?php foreach ($slides as $i => $_): ?>
                <button type="button" class="xr-wave-slider__dot<?= $i === 0 ? ' is-active' : '' ?>" data-carousel-dot aria-label="Slide <?= (int) ($i + 1) ?>"></button>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

function xr_block_layered_star(array $p, string $blockId = ''): void
{
    $title = (string) ($p['title'] ?? '');
    $subtitle = (string) ($p['subtitle'] ?? '');
    $base = (string) ($p['base_color'] ?? '#151a22');
    ?>
    <div class="xr-layered-star" style="--xr-layer-base: <?= h($base) ?>">
        <div class="xr-layered-star__bg" aria-hidden="true"></div>
        <canvas class="xr-layered-star__canvas" data-starfield width="800" height="500" aria-hidden="true"></canvas>
        <div class="xr-layered-star__gradient" aria-hidden="true"></div>
        <div class="xr-layered-star__content">
            <h2 class="xr-layered-star__title xr-burst-text"><?= h($title) ?></h2>
            <p class="xr-layered-star__subtitle xr-burst-text"><?= h($subtitle) ?></p>
        </div>
    </div>
    <?php
}

function xr_block_tabs_youtube_loop(array $p, string $blockId = ''): void
{
    $heading = (string) ($p['heading'] ?? '');
    $tabs = is_array($p['tabs'] ?? null) ? $p['tabs'] : [];
    $headingLg = ((string) ($p['heading_size'] ?? '')) === 'lg';
    $rootClass = 'xr-tabs-media' . ($headingLg ? ' xr-tabs-media--heading-lg' : '');
    ?>
    <div class="<?= h($rootClass) ?>">
        <?php if ($heading !== ''): ?>
            <h2 class="xr-tabs-media__heading"><?= h($heading) ?></h2>
        <?php endif; ?>
        <div class="xr-tabs-media__bar" role="tablist">
            <?php foreach ($tabs as $i => $t): ?>
                <?php if (!is_array($t)) {
                    continue;
                } ?>
                <button type="button" role="tab" class="xr-tabs-media__tab<?= $i === 0 ? ' is-active' : '' ?>"
                        id="xr-tm-tab-<?= (int) $i ?>"
                        aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"
                        aria-controls="xr-tm-panel-<?= (int) $i ?>"
                        data-xr-tab="media"
                        data-index="<?= (int) $i ?>"><?= h((string) ($t['label'] ?? '')) ?></button>
            <?php endforeach; ?>
        </div>
        <?php foreach ($tabs as $i => $t): ?>
            <?php if (!is_array($t)) {
                continue;
            } ?>
            <?php
            $mode = (string) ($t['mode'] ?? 'youtube_click');
            $poster = (string) ($t['poster'] ?? '');
            $yt = (string) ($t['youtube_id'] ?? '');
            $mp4 = (string) ($t['mp4'] ?? '');
            ?>
            <div class="xr-tabs-media__panel<?= $i === 0 ? ' is-active' : '' ?>" role="tabpanel"
                 id="xr-tm-panel-<?= (int) $i ?>"
                 aria-labelledby="xr-tm-tab-<?= (int) $i ?>"
                 data-xr-panel="media"
                 data-index="<?= (int) $i ?>">
                <div class="xr-tabs-media__stage xr-tabs-media__stage--<?= h(in_array($mode, ['youtube_click', 'video_loop'], true) ? $mode : 'youtube_click') ?>">
                    <?php if ($mode === 'youtube_click'): ?>
                        <div class="xr-yt-mask">
                            <img src="<?= h($poster) ?>" alt="">
                            <button type="button" class="xr-yt-play btn btn--gradient" data-youtube-load="<?= h($yt) ?>"><?= h((string) ($t['play_label'] ?? 'Play')) ?></button>
                        </div>
                        <div class="xr-yt-frame" hidden data-youtube-frame></div>
                    <?php elseif ($mode === 'video_loop'): ?>
                        <video class="xr-loop-video" data-video-loop muted playsinline autoplay loop poster="<?= h($poster) ?>">
                            <?php if ($mp4 !== ''): ?><source src="<?= h($mp4) ?>" type="video/mp4"><?php endif; ?>
                        </video>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
}

function xr_block_video_freeze_section(array $p, string $blockId = ''): void
{
    $mp4 = (string) ($p['mp4'] ?? '');
    $poster = (string) ($p['poster'] ?? '');
    $caption = (string) ($p['caption'] ?? '');
    if ($mp4 === '') {
        return;
    }
    ?>
    <div class="xr-video-freeze">
        <video class="xr-video-freeze__video" muted playsinline data-video-freeze poster="<?= h($poster) ?>">
            <source src="<?= h($mp4) ?>" type="video/mp4">
        </video>
        <?php if ($caption !== ''): ?>
            <p class="xr-video-freeze__cap"><?= h($caption) ?></p>
        <?php endif; ?>
    </div>
    <?php
}

function xr_block_video_freeze_center_image(array $p, string $blockId = ''): void
{
    $mp4 = (string) ($p['mp4'] ?? '');
    $poster = (string) ($p['poster'] ?? '');
    $caption = (string) ($p['caption'] ?? '');
    $center = (string) ($p['center_image'] ?? '');
    $alt = (string) ($p['center_alt'] ?? '');
    if ($mp4 === '') {
        return;
    }
    ?>
    <div class="xr-vfreeze-center">
        <div class="xr-vfreeze-center__video-wrap">
            <video class="xr-vfreeze-center__video xr-video-freeze__video" muted playsinline data-video-freeze poster="<?= h($poster) ?>">
                <source src="<?= h($mp4) ?>" type="video/mp4">
            </video>
            <?php if ($center !== ''): ?>
                <div class="xr-vfreeze-center__mid" aria-hidden="true">
                    <img class="xr-vfreeze-center__mid-img" src="<?= h($center) ?>" alt="<?= h($alt) ?>">
                </div>
            <?php endif; ?>
        </div>
        <?php if ($caption !== ''): ?>
            <p class="xr-vfreeze-center__cap"><?= h($caption) ?></p>
        <?php endif; ?>
    </div>
    <?php
}

function xr_block_product_tabs(array $p, string $blockId = ''): void
{
    $tabs = is_array($p['tabs'] ?? null) ? $p['tabs'] : [];
    $panels = is_array($p['panels'] ?? null) ? $p['panels'] : [];
    $active = (int) ($p['active_tab'] ?? 0);
    ?>
    <div class="xr-product-tabs">
        <div class="xr-product-tabs__bar" role="tablist">
            <?php foreach ($tabs as $i => $label): ?>
                <button type="button" role="tab" class="xr-product-tabs__tab<?= $i === $active ? ' is-active' : '' ?>"
                        data-xr-tab="product" data-index="<?= (int) $i ?>"
                        aria-selected="<?= $i === $active ? 'true' : 'false' ?>"><?= h((string) $label) ?></button>
            <?php endforeach; ?>
        </div>
        <?php foreach ($panels as $i => $panel): ?>
            <?php if (!is_array($panel)) {
                continue;
            } ?>
            <div class="xr-product-tabs__panel<?= $i === $active ? ' is-active' : '' ?>" data-xr-panel="product" data-index="<?= (int) $i ?>">
                <h3 class="xr-product-tabs__title"><?= h((string) ($panel['title'] ?? '')) ?></h3>
                <p class="xr-product-tabs__lead"><?= h((string) ($panel['lead'] ?? '')) ?></p>
                <p class="xr-product-tabs__body"><?= h((string) ($panel['body'] ?? '')) ?></p>
                <p class="xr-product-tabs__emph"><?= h((string) ($panel['emphasis'] ?? '')) ?></p>
                <div class="xr-product-tabs__split">
                    <div class="xr-product-tabs__feats">
                        <?php
                        $feats = is_array($panel['features'] ?? null) ? $panel['features'] : [];
                        foreach ($feats as $f) {
                            $lab = is_array($f) ? ($f['label'] ?? '') : '';
                            echo '<div class="xr-product-tabs__feat"><span class="xr-dot"></span>' . h((string) $lab) . '</div>';
                        }
                        ?>
                    </div>
                    <p class="xr-product-tabs__side"><?= h((string) ($panel['sidebar'] ?? '')) ?></p>
                </div>
                <div class="xr-product-tabs__bottom">
                    <p><?= h((string) ($panel['bottom_title'] ?? '')) ?></p>
                    <?php
                    $lnk = is_array($panel['bottom_link'] ?? null) ? $panel['bottom_link'] : [];
                    ?>
                    <a class="xr-product-tabs__link" href="<?= h((string) ($lnk['href'] ?? '#')) ?>"><?= h((string) ($lnk['label'] ?? '')) ?> →</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
}

function xr_block_testimonials_marquee(array $p, string $blockId = ''): void
{
    $items = is_array($p['items'] ?? null) ? $p['items'] : [];
    $speed = (float) ($p['speed'] ?? 40);
    if ($items === []) {
        return;
    }
    $dup = array_merge($items, $items);
    ?>
    <div class="xr-marquee" data-marquee-speed="<?= h((string) $speed) ?>">
        <div class="xr-marquee__track">
            <?php foreach ($dup as $i => $it): ?>
                <?php if (!is_array($it)) {
                    continue;
                } ?>
                <article class="xr-marquee__card" data-review-index="<?= (int) ($i % max(1, count($items))) ?>">
                    <p class="xr-marquee__quote">“<?= h((string) ($it['quote'] ?? '')) ?>”</p>
                    <footer class="xr-marquee__meta"><?= h((string) ($it['author'] ?? '')) ?> — <?= h((string) ($it['role'] ?? '')) ?></footer>
                    <button type="button" class="xr-marquee__more" data-review-open="<?= (int) ($i % max(1, count($items))) ?>">Read more</button>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="xr-modal" id="xr-review-<?= h($blockId) ?>" hidden data-review-modal data-review-root="<?= h($blockId) ?>">
        <div class="xr-modal__backdrop" data-review-close></div>
        <div class="xr-modal__dialog" role="dialog" aria-modal="true">
            <button type="button" class="xr-modal__x" data-review-close aria-label="Close">×</button>
            <div class="xr-modal__body" data-review-body></div>
            <div class="xr-modal__nav">
                <button type="button" data-review-prev aria-label="Previous">←</button>
                <button type="button" data-review-next aria-label="Next">→</button>
            </div>
        </div>
    </div>
    <script type="application/json" data-review-json data-review-for="<?= h($blockId) ?>"><?= json_encode($items, JSON_UNESCAPED_UNICODE) ?></script>
    <?php
    $cross = $p['cross_link'] ?? null;
    if (is_array($cross) && !empty($cross['href'])) {
        ?>
        <div class="xr-marquee__cross">
            <a class="xr-marquee__cross-link" href="<?= h((string) $cross['href']) ?>"><?= h((string) ($cross['label'] ?? 'Blog')) ?></a>
        </div>
        <?php
    }
}

function xr_block_counters_row(array $p, string $blockId = ''): void
{
    $items = is_array($p['items'] ?? null) ? $p['items'] : [];
    ?>
    <div class="xr-counters">
        <?php foreach ($items as $it): ?>
            <?php if (!is_array($it)) {
                continue;
            } ?>
            <div class="xr-counter">
                <div class="xr-counter__ring" aria-hidden="true">
                    <span class="xr-counter__value"><?= h((string) ($it['value'] ?? '')) ?></span>
                </div>
                <div class="xr-counter__label"><?= h((string) ($it['label'] ?? '')) ?></div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
}

function xr_block_floating_plank(array $p, string $blockId = ''): void
{
    $title = (string) ($p['title'] ?? '');
    $bullets = is_array($p['bullets'] ?? null) ? $p['bullets'] : [];
    $img = (string) ($p['image'] ?? '');
    ?>
    <div class="xr-float-plank">
        <div class="xr-float-plank__rail" data-float-plank>
            <div class="xr-float-plank__card">
                <h3><?= h($title) ?></h3>
                <ul>
                    <?php foreach ($bullets as $b): ?>
                        <li><?= h((string) $b) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="xr-float-plank__visual">
            <img src="<?= h($img) ?>" alt="">
        </div>
    </div>
    <?php
}

function xr_block_timeline_gradient(array $p, string $blockId = ''): void
{
    $heading = (string) ($p['heading'] ?? '');
    $steps = is_array($p['steps'] ?? null) ? $p['steps'] : [];
    ?>
    <div class="xr-timeline">
        <h2 class="xr-timeline__head"><?= h($heading) ?></h2>
        <ol class="xr-timeline__list">
            <?php foreach ($steps as $s): ?>
                <?php if (!is_array($s)) {
                    continue;
                } ?>
                <li class="xr-timeline__step">
                    <span class="xr-timeline__dot"></span>
                    <div class="xr-timeline__text">
                        <strong><?= h((string) ($s['title'] ?? '')) ?></strong>
                        <p><?= h((string) ($s['text'] ?? '')) ?></p>
                    </div>
                </li>
            <?php endforeach; ?>
        </ol>
    </div>
    <?php
}

function xr_block_gallery_three(array $p, string $blockId = ''): void
{
    $heading = (string) ($p['heading'] ?? '');
    $slides = is_array($p['slides'] ?? null) ? $p['slides'] : [];
    $interval = (int) ($p['interval_ms'] ?? 4000);
    ?>
    <div class="xr-g3" data-carousel data-interval="<?= (int) $interval ?>">
        <h2 class="xr-g3__head"><?= h($heading) ?></h2>
        <div class="xr-g3__viewport">
            <?php foreach ($slides as $i => $s): ?>
                <?php if (!is_array($s)) {
                    continue;
                } ?>
                <figure class="xr-g3__slide<?= $i === 0 ? ' is-active' : '' ?>" data-carousel-slide>
                    <img src="<?= h((string) ($s['image'] ?? '')) ?>" alt="">
                    <figcaption><?= h((string) ($s['title'] ?? '')) ?></figcaption>
                </figure>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

function xr_block_starfield_cta(array $p, string $blockId = ''): void
{
    $sym = (string) ($p['infinity_symbol'] ?? '∞');
    $title = (string) ($p['title'] ?? '');
    $btn = (string) ($p['button_label'] ?? '');
    $href = (string) ($p['href'] ?? '#');
    ?>
    <div class="xr-star-cta">
        <canvas class="xr-star-cta__canvas" data-starfield-cta width="1200" height="420" aria-hidden="true"></canvas>
        <div class="xr-star-cta__sym" data-infinity-sway><?= h($sym) ?></div>
        <h2 class="xr-star-cta__title xr-reveal"><?= h($title) ?></h2>
        <a class="btn btn--gradient xr-star-cta__btn xr-pulse-btn" href="<?= h($href) ?>"><?= h($btn) ?></a>
    </div>
    <?php
}

function xr_block_text_reveal_simple(array $p, string $blockId = ''): void
{
    $lines = is_array($p['lines'] ?? null) ? $p['lines'] : [];
    ?>
    <div class="xr-text-reveal">
        <?php foreach ($lines as $ln): ?>
            <p class="xr-text-reveal__line xr-reveal"><?= h((string) $ln) ?></p>
        <?php endforeach; ?>
    </div>
    <?php
}

function xr_block_closing_block(array $p, string $blockId = ''): void
{
    ?>
    <div class="xr-closing">
        <p class="xr-closing__small xr-reveal"><?= h((string) ($p['line1'] ?? '')) ?></p>
        <p class="xr-closing__big xr-reveal"><?= h((string) ($p['line2'] ?? '')) ?></p>
    </div>
    <?php
}

/* ——— Professionals ——— */

function xr_block_hero_twinkle(array $p, string $blockId = ''): void
{
    $img = (string) ($p['image'] ?? '');
    $title = (string) ($p['title'] ?? '');
    $sub = (string) ($p['subtitle'] ?? '');
    ?>
    <div class="xr-hero-twinkle">
        <div class="xr-hero-twinkle__media">
            <img src="<?= h($img) ?>" alt="">
            <canvas class="xr-hero-twinkle__tw" data-twinkle-overlay width="1600" height="900" aria-hidden="true"></canvas>
        </div>
        <div class="xr-hero-twinkle__text">
            <h1 class="xr-reveal"><?= h($title) ?></h1>
            <p class="xr-reveal"><?= h($sub) ?></p>
        </div>
    </div>
    <?php
}

function xr_block_blue_video_freeze(array $p, string $blockId = ''): void
{
    $mp4 = (string) ($p['mp4'] ?? '');
    $poster = (string) ($p['poster'] ?? '');
    $text = (string) ($p['text'] ?? '');
    ?>
    <div class="xr-blue-freeze">
        <p class="xr-blue-freeze__text"><?= h($text) ?></p>
        <div class="xr-blue-freeze__video">
            <video class="xr-video-freeze__video" muted playsinline data-video-freeze poster="<?= h($poster) ?>">
                <source src="<?= h($mp4) ?>" type="video/mp4">
            </video>
        </div>
    </div>
    <?php
}

function xr_block_partner_split_video(array $p, string $blockId = ''): void
{
    $mp4 = (string) ($p['mp4'] ?? '');
    $poster = (string) ($p['poster'] ?? '');
    $title = (string) ($p['title'] ?? '');
    $body = (string) ($p['body'] ?? '');
    $btn = (string) ($p['button_label'] ?? '');
    $href = (string) ($p['href'] ?? '#');
    $flip = ((string) ($p['media_position'] ?? 'left')) === 'right';
    ?>
    <div class="xr-partner-split<?= $flip ? ' xr-partner-split--flip' : '' ?>">
        <div class="xr-partner-split__media">
            <?php if ($mp4 !== ''): ?>
                <video class="xr-video-freeze__video" muted playsinline data-video-freeze poster="<?= h($poster) ?>">
                    <source src="<?= h($mp4) ?>" type="video/mp4">
                </video>
            <?php elseif ($poster !== ''): ?>
                <img class="xr-partner-split__poster" src="<?= h($poster) ?>" alt="">
            <?php endif; ?>
        </div>
        <div class="xr-partner-split__copy">
            <?php if ($title !== ''): ?>
                <h2 class="xr-partner-split__title"><?= h($title) ?></h2>
            <?php endif; ?>
            <?php if ($body !== ''): ?>
                <p class="xr-partner-split__body"><?= h($body) ?></p>
            <?php endif; ?>
            <?php if ($btn !== ''): ?>
                <a class="btn btn--gradient xr-partner-split__btn" href="<?= h($href) ?>"><?= h($btn) ?></a>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

function xr_block_partner_wp_icons(array $p, string $blockId = ''): void
{
    $title = (string) ($p['title'] ?? '');
    $items = is_array($p['items'] ?? null) ? $p['items'] : [];
    ?>
    <div class="xr-partner-wpicons">
        <?php if ($title !== ''): ?>
            <h2 class="xr-partner-wpicons__title"><?= h($title) ?></h2>
        <?php endif; ?>
        <?php
        $note = trim((string) ($p['note'] ?? ''));
        if ($note !== ''):
            ?>
            <p class="xr-partner-wpicons__note"><?= h($note) ?></p>
        <?php endif; ?>
        <div class="xr-partner-wpicons__row">
            <?php foreach ($items as $it): ?>
                <?php if (!is_array($it)) {
                    continue;
                } ?>
                <?php
                $glyph = preg_replace('/[^0-9a-f]/i', '', (string) ($it['dashicon'] ?? ''));
                if ($glyph === '') {
                    $glyph = 'f120';
                }
                ?>
                <article class="xr-partner-wpicons__card">
                    <span class="xr-partner-wpicons__glyph" data-glyph="<?= h(strtolower($glyph)) ?>" aria-hidden="true"></span>
                    <h3><?= h((string) ($it['label'] ?? '')) ?></h3>
                    <p><?= h((string) ($it['text'] ?? '')) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

function xr_block_two_column_features(array $p, string $blockId = ''): void
{
    ?>
    <div class="xr-two-col">
        <div class="xr-two-col__text">
            <h2><?= h((string) ($p['left_title'] ?? '')) ?></h2>
            <p><?= h((string) ($p['left_text'] ?? '')) ?></p>
        </div>
        <div class="xr-two-col__img">
            <img src="<?= h((string) ($p['right_image'] ?? '')) ?>" alt="">
        </div>
    </div>
    <?php
}

function xr_block_carousel_four_cards(array $p, string $blockId = ''): void
{
    $cards = is_array($p['cards'] ?? null) ? $p['cards'] : [];
    ?>
    <div class="xr-cards4" data-cards4>
        <div class="xr-cards4__track">
            <?php for ($dup = 0; $dup < 2; $dup++): ?>
                <?php foreach ($cards as $i => $c): ?>
                    <?php if (!is_array($c)) {
                        continue;
                    } ?>
                    <article class="xr-cards4__card">
                        <img src="<?= h((string) ($c['image'] ?? '')) ?>" alt="">
                        <h3><?= h((string) ($c['title'] ?? '')) ?></h3>
                        <p><?= h((string) ($c['text'] ?? '')) ?></p>
                    </article>
                <?php endforeach; ?>
            <?php endfor; ?>
        </div>
    </div>
    <?php
}

function xr_block_youtube_heading(array $p, string $blockId = ''): void
{
    $h = (string) ($p['heading'] ?? '');
    $id = (string) ($p['youtube_id'] ?? '');
    ?>
    <div class="xr-yt-head">
        <h2 class="xr-yt-head__title xr-reveal"><?= h($h) ?></h2>
        <div class="xr-yt-head__embed">
            <iframe src="https://www.youtube.com/embed/<?= h($id) ?>" title="YouTube" loading="lazy" allowfullscreen></iframe>
        </div>
    </div>
    <?php
}

function xr_block_animated_heading_tabs(array $p, string $blockId = ''): void
{
    $prefix = (string) ($p['prefix'] ?? '');
    $rot = is_array($p['rotating'] ?? null) ? $p['rotating'] : [];
    $interval = (int) ($p['interval_ms'] ?? 2800);
    $panels = is_array($p['panels'] ?? null) ? $p['panels'] : [];
    ?>
    <div class="xr-anim-head-tabs" data-rotating-head data-interval="<?= (int) $interval ?>">
        <script type="application/json" data-rotating-words><?= json_encode(array_values($rot), JSON_UNESCAPED_UNICODE) ?></script>
        <div class="xr-anim-head-tabs__headline">
            <span><?= h($prefix) ?></span>
            <span class="xr-anim-head-tabs__rot" data-rotating-target><?= h((string) ($rot[0] ?? '')) ?></span>
        </div>
        <div class="xr-anim-head-tabs__bar" role="tablist">
            <?php foreach ($panels as $i => $pan): ?>
                <?php if (!is_array($pan)) {
                    continue;
                } ?>
                <button type="button" role="tab" class="xr-anim-head-tabs__tab<?= $i === 0 ? ' is-active' : '' ?>"
                        data-xr-tab="aht" data-index="<?= (int) $i ?>"><?= h((string) ($pan['label'] ?? '')) ?></button>
            <?php endforeach; ?>
        </div>
        <?php foreach ($panels as $i => $pan): ?>
            <?php if (!is_array($pan)) {
                continue;
            } ?>
            <div class="xr-anim-head-tabs__panel<?= $i === 0 ? ' is-active' : '' ?>" data-xr-panel="aht" data-index="<?= (int) $i ?>">
                <p><?= h((string) ($pan['text'] ?? '')) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
}

function xr_block_tabs_three_horizontal(array $p, string $blockId = ''): void
{
    $tabs = is_array($p['tabs'] ?? null) ? $p['tabs'] : [];
    ?>
    <div class="xr-tabs3h">
        <div class="xr-tabs3h__bar" role="tablist">
            <?php foreach ($tabs as $i => $t): ?>
                <?php if (!is_array($t)) {
                    continue;
                } ?>
                <button type="button" role="tab" class="xr-tabs3h__tab<?= $i === 0 ? ' is-active' : '' ?>"
                        data-xr-tab="t3" data-index="<?= (int) $i ?>"><?= h((string) ($t['label'] ?? '')) ?></button>
            <?php endforeach; ?>
        </div>
        <?php foreach ($tabs as $i => $t): ?>
            <?php if (!is_array($t)) {
                continue;
            } ?>
            <div class="xr-tabs3h__panel<?= $i === 0 ? ' is-active' : '' ?>" data-xr-panel="t3" data-index="<?= (int) $i ?>">
                <p><?= h((string) ($t['body'] ?? '')) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
}

function xr_block_text_heading_anim(array $p, string $blockId = ''): void
{
    ?>
    <div class="xr-tha">
        <h2 class="xr-tha__h xr-reveal"><?= h((string) ($p['headline'] ?? '')) ?></h2>
        <p class="xr-tha__p xr-reveal"><?= h((string) ($p['paragraph'] ?? '')) ?></p>
    </div>
    <?php
}

function xr_block_tabs_top_images(array $p, string $blockId = ''): void
{
    $imgs = is_array($p['images'] ?? null) ? $p['images'] : [];
    $tabs = is_array($p['tabs'] ?? null) ? $p['tabs'] : [];
    ?>
    <div class="xr-tti">
        <div class="xr-tti__imgs">
            <?php foreach ($imgs as $u): ?>
                <img src="<?= h((string) $u) ?>" alt="">
            <?php endforeach; ?>
        </div>
        <div class="xr-tti__bar" role="tablist">
            <?php foreach ($tabs as $i => $t): ?>
                <?php if (!is_array($t)) {
                    continue;
                } ?>
                <button type="button" class="xr-tti__tab<?= $i === 0 ? ' is-active' : '' ?>" data-xr-tab="tti" data-index="<?= (int) $i ?>"><?= h((string) ($t['label'] ?? '')) ?></button>
            <?php endforeach; ?>
        </div>
        <?php foreach ($tabs as $i => $t): ?>
            <?php if (!is_array($t)) {
                continue;
            } ?>
            <div class="xr-tti__panel<?= $i === 0 ? ' is-active' : '' ?>" data-xr-panel="tti" data-index="<?= (int) $i ?>">
                <p><?= h((string) ($t['body'] ?? '')) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
}

function xr_block_pricing_creative(array $p, string $blockId = ''): void
{
    $plans = is_array($p['plans'] ?? null) ? $p['plans'] : [];
    ?>
    <div class="xr-pricing">
        <?php foreach ($plans as $pl): ?>
            <?php if (!is_array($pl)) {
                continue;
            } ?>
            <div class="xr-pricing__card<?= !empty($pl['highlight']) ? ' is-highlight' : '' ?>">
                <h3><?= h((string) ($pl['name'] ?? '')) ?></h3>
                <div class="xr-pricing__price"><?= h((string) ($pl['price'] ?? '')) ?></div>
                <ul>
                    <?php
                    $feats = is_array($pl['features'] ?? null) ? $pl['features'] : [];
                    foreach ($feats as $f) {
                        echo '<li>' . h((string) $f) . '</li>';
                    }
                    ?>
                </ul>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
}

function xr_block_coming_soon_anim(array $p, string $blockId = ''): void
{
    ?>
    <div class="xr-cs-anim" data-cs-anim>
        <p class="xr-cs-anim__a"><?= h((string) ($p['line1'] ?? '')) ?></p>
        <p class="xr-cs-anim__b"><?= h((string) ($p['line2'] ?? '')) ?></p>
    </div>
    <?php
}

function xr_block_stagger_lines(array $p, string $blockId = ''): void
{
    $pairs = is_array($p['pairs'] ?? null) ? $p['pairs'] : [];
    ?>
    <div class="xr-stagger">
        <?php foreach ($pairs as $pair): ?>
            <?php if (!is_array($pair)) {
                continue;
            } ?>
            <?php $lines = is_array($pair['lines'] ?? null) ? $pair['lines'] : []; ?>
            <div class="xr-stagger__pair">
                <?php foreach ($lines as $ln): ?>
                    <p class="xr-stagger__line xr-reveal"><?= h((string) $ln) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
}

function xr_block_image_pulse_cta(array $p, string $blockId = ''): void
{
    ?>
    <div class="xr-ipc">
        <img src="<?= h((string) ($p['image'] ?? '')) ?>" alt="">
        <div class="xr-ipc__cap">
            <p><?= h((string) ($p['text'] ?? '')) ?></p>
            <a class="btn btn--gradient xr-pulse-btn" href="<?= h((string) ($p['href'] ?? '#')) ?>"><?= h((string) ($p['button_label'] ?? '')) ?></a>
        </div>
    </div>
    <?php
}

function xr_block_reveal_outro(array $p, string $blockId = ''): void
{
    ?>
    <div class="xr-outro">
        <h2 class="xr-reveal"><?= h((string) ($p['title'] ?? '')) ?></h2>
        <p class="xr-reveal"><?= h((string) ($p['body'] ?? '')) ?></p>
    </div>
    <?php
}

function xr_block_carousel_two(array $p, string $blockId = ''): void
{
    $slides = is_array($p['slides'] ?? null) ? $p['slides'] : [];
    $interval = (int) ($p['interval_ms'] ?? 4500);
    $heading = (string) ($p['heading'] ?? '');
    ?>
    <div class="xr-carousel-two" data-carousel data-interval="<?= (int) $interval ?>">
        <?php if ($heading !== ''): ?>
            <h2 class="xr-carousel-two__head"><?= h($heading) ?></h2>
        <?php endif; ?>
        <div class="xr-carousel-two__viewport">
            <?php foreach ($slides as $i => $s): ?>
                <?php if (!is_array($s)) {
                    continue;
                } ?>
                <figure class="xr-carousel-two__slide<?= $i === 0 ? ' is-active' : '' ?>" data-carousel-slide>
                    <img src="<?= h((string) ($s['image'] ?? '')) ?>" alt="">
                    <?php if (!empty($s['caption'])): ?>
                        <figcaption><?= h((string) $s['caption']) ?></figcaption>
                    <?php endif; ?>
                </figure>
            <?php endforeach; ?>
        </div>
        <div class="xr-carousel-two__dots">
            <?php foreach ($slides as $i => $_): ?>
                <button type="button" class="xr-carousel-two__dot<?= $i === 0 ? ' is-active' : '' ?>" data-carousel-dot aria-label="Slide <?= (int) ($i + 1) ?>"></button>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

function xr_block_white_text_section(array $p, string $blockId = ''): void
{
    ?>
    <div class="xr-white-section">
        <div class="xr-white-section__inner">
            <h2><?= h((string) ($p['title'] ?? '')) ?></h2>
            <p><?= h((string) ($p['body'] ?? '')) ?></p>
        </div>
    </div>
    <?php
}

function xr_block_before_after(array $p, string $blockId = ''): void
{
    $before = is_array($p['before'] ?? null) ? $p['before'] : [];
    $after = is_array($p['after'] ?? null) ? $p['after'] : [];
    $bid = preg_replace('/[^a-zA-Z0-9_-]/', '', $blockId) ?: 'ba';
    ?>
    <div class="xr-ba" data-before-after="<?= h($bid) ?>">
        <h2 class="xr-ba__title"><?= h((string) ($p['title'] ?? '')) ?></h2>
        <div class="xr-ba__stage">
            <img class="xr-ba__img xr-ba__img--after" src="<?= h((string) ($after['image'] ?? '')) ?>" alt="">
            <div class="xr-ba__clip" data-ba-clip>
                <img class="xr-ba__img xr-ba__img--before" src="<?= h((string) ($before['image'] ?? '')) ?>" alt="">
            </div>
            <div class="xr-ba__handle" data-ba-handle tabindex="0" role="slider" aria-valuemin="0" aria-valuemax="100" aria-valuenow="50" aria-label="Before and after"></div>
        </div>
        <div class="xr-ba__labels">
            <span><?= h((string) ($before['label'] ?? 'Before')) ?></span>
            <span><?= h((string) ($after['label'] ?? 'After')) ?></span>
        </div>
        <input type="range" class="xr-ba__range" data-ba-range min="0" max="100" value="50" aria-hidden="true">
    </div>
    <?php
}

function xr_block_saas_split(array $p, string $blockId = ''): void
{
    ?>
    <div class="xr-saas">
        <div class="xr-saas__inner">
            <p class="xr-saas__eyebrow"><?= h((string) ($p['eyebrow'] ?? '')) ?></p>
            <h2 class="xr-saas__title"><?= h((string) ($p['title'] ?? '')) ?></h2>
            <p class="xr-saas__text"><?= h((string) ($p['text'] ?? '')) ?></p>
            <a class="btn btn--gradient" href="<?= h((string) ($p['href'] ?? '#')) ?>"><?= h((string) ($p['button_label'] ?? '')) ?></a>
        </div>
    </div>
    <?php
}

function xr_block_orbit_cards(array $p, string $blockId = ''): void
{
    $cards = is_array($p['cards'] ?? null) ? $p['cards'] : [];
    ?>
    <div class="xr-orbit" data-orbit>
        <h2 class="xr-orbit__title"><?= h((string) ($p['title'] ?? '')) ?></h2>
        <div class="xr-orbit__stage">
            <div class="xr-orbit__center"><?= h((string) ($p['center_label'] ?? '')) ?></div>
            <?php foreach ($cards as $i => $c): ?>
                <?php if (!is_array($c)) {
                    continue;
                } ?>
                <div class="xr-orbit__card" style="--i: <?= (int) $i ?>; --n: <?= max(1, count($cards)) ?>">
                    <span class="xr-orbit__card-label"><?= h((string) ($c['label'] ?? '')) ?></span>
                    <span class="xr-orbit__card-sub"><?= h((string) ($c['sub'] ?? '')) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

function xr_block_white_planks(array $p, string $blockId = ''): void
{
    $planks = is_array($p['planks'] ?? null) ? $p['planks'] : [];
    $stagger = !empty($p['stagger']);
    $rootClass = 'xr-planks' . ($stagger ? ' xr-planks--stagger' : '');
    ?>
    <div class="<?= h($rootClass) ?>">
        <h2 class="xr-planks__title"><?= h((string) ($p['title'] ?? '')) ?></h2>
        <div class="xr-planks__row">
            <?php foreach ($planks as $pl): ?>
                <?php if (!is_array($pl)) {
                    continue;
                } ?>
                <article class="xr-planks__item<?= $stagger ? ' xr-reveal' : '' ?>">
                    <h3><?= h((string) ($pl['title'] ?? '')) ?></h3>
                    <p><?= h((string) ($pl['text'] ?? '')) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

function xr_block_pricing_swapped(array $p, string $blockId = ''): void
{
    $heading = (string) ($p['heading'] ?? '');
    $plans = is_array($p['plans'] ?? null) ? $p['plans'] : [];
    ?>
    <div class="xr-pricing xr-pricing--swapped">
        <?php if ($heading !== ''): ?>
            <h2 class="xr-pricing__page-title"><?= h($heading) ?></h2>
        <?php endif; ?>
        <?php foreach ($plans as $pl): ?>
            <?php if (!is_array($pl)) {
                continue;
            } ?>
            <div class="xr-pricing__card<?= !empty($pl['highlight']) ? ' is-highlight' : '' ?>">
                <h3><?= h((string) ($pl['name'] ?? '')) ?></h3>
                <div class="xr-pricing__price"><?= h((string) ($pl['price'] ?? '')) ?></div>
                <ul>
                    <?php
                    $feats = is_array($pl['features'] ?? null) ? $pl['features'] : [];
                    foreach ($feats as $f) {
                        echo '<li>' . h((string) $f) . '</li>';
                    }
                    ?>
                </ul>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
}

function xr_block_tabs_dual_carousel(array $p, string $blockId = ''): void
{
    $tabs = is_array($p['tabs'] ?? null) ? $p['tabs'] : [];
    $g = 'g' . preg_replace('/[^a-z0-9]/i', '', $blockId);
    ?>
    <div class="xr-dual-tabs">
        <?php if (!empty($p['heading'])): ?>
            <h2 class="xr-dual-tabs__head"><?= h((string) $p['heading']) ?></h2>
        <?php endif; ?>
        <div class="xr-dual-tabs__bar" role="tablist">
            <?php foreach ($tabs as $i => $t): ?>
                <?php if (!is_array($t)) {
                    continue;
                } ?>
                <button type="button" role="tab" class="xr-dual-tabs__tab<?= $i === 0 ? ' is-active' : '' ?>"
                        data-xr-tab="<?= h($g) ?>" data-index="<?= (int) $i ?>"><?= h((string) ($t['label'] ?? '')) ?></button>
            <?php endforeach; ?>
        </div>
        <?php foreach ($tabs as $i => $t): ?>
            <?php if (!is_array($t)) {
                continue;
            } ?>
            <?php $slides = is_array($t['slides'] ?? null) ? $t['slides'] : []; ?>
            <div class="xr-dual-tabs__panel<?= $i === 0 ? ' is-active' : '' ?>" data-xr-panel="<?= h($g) ?>" data-index="<?= (int) $i ?>">
                <div class="xr-inner-carousel" data-inner-carousel>
                    <?php foreach ($slides as $j => $sl): ?>
                        <?php if (!is_array($sl)) {
                            continue;
                        } ?>
                        <?php
                        $iv = (int) ($sl['interval_ms'] ?? 5000);
                        $mp4 = (string) ($sl['mp4'] ?? '');
                        ?>
                        <div class="xr-inner-carousel__slide<?= $j === 0 ? ' is-active' : '' ?>" data-inner-slide data-interval="<?= $iv ?>">
                            <?php if ($mp4 !== ''): ?>
                                <video class="xr-inner-carousel__vid" muted playsinline loop>
                                    <source src="<?= h($mp4) ?>" type="video/mp4">
                                </video>
                            <?php else: ?>
                                <img src="<?= h((string) ($sl['image'] ?? '')) ?>" alt="">
                            <?php endif; ?>
                            <p class="xr-inner-carousel__cap"><?= h((string) ($sl['text'] ?? '')) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
}

function xr_block_tabs_two_plain(array $p, string $blockId = ''): void
{
    $tabs = is_array($p['tabs'] ?? null) ? $p['tabs'] : [];
    $g = 't2' . preg_replace('/[^a-z0-9]/i', '', $blockId);
    ?>
    <div class="xr-tabs2">
        <div class="xr-tabs2__bar" role="tablist">
            <?php foreach ($tabs as $i => $t): ?>
                <?php if (!is_array($t)) {
                    continue;
                } ?>
                <button type="button" class="xr-tabs2__tab<?= $i === 0 ? ' is-active' : '' ?>"
                        data-xr-tab="<?= h($g) ?>" data-index="<?= (int) $i ?>"><?= h((string) ($t['label'] ?? '')) ?></button>
            <?php endforeach; ?>
        </div>
        <?php foreach ($tabs as $i => $t): ?>
            <?php if (!is_array($t)) {
                continue;
            } ?>
            <div class="xr-tabs2__panel<?= $i === 0 ? ' is-active' : '' ?>" data-xr-panel="<?= h($g) ?>" data-index="<?= (int) $i ?>">
                <p><?= h((string) ($t['body'] ?? '')) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
}

function xr_block_progress_bars_block(array $p, string $blockId = ''): void
{
    $bars = is_array($p['bars'] ?? null) ? $p['bars'] : [];
    ?>
    <div class="xr-pbars" data-progress-bars>
        <h2 class="xr-pbars__title"><?= h((string) ($p['title'] ?? '')) ?></h2>
        <?php foreach ($bars as $b): ?>
            <?php if (!is_array($b)) {
                continue;
            } ?>
            <div class="xr-pbars__row">
                <div class="xr-pbars__label"><?= h((string) ($b['label'] ?? '')) ?></div>
                <div class="xr-pbars__track">
                    <div class="xr-pbars__fill" data-pbar-fill data-target="<?= (int) ($b['value'] ?? 0) ?>"></div>
                </div>
                <span class="xr-pbars__pct"><?= (int) ($b['value'] ?? 0) ?>%</span>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
}

function xr_block_highlight_box_block(array $p, string $blockId = ''): void
{
    ?>
    <div class="xr-hlbox">
        <div class="xr-hlbox__inner">
            <h2><?= h((string) ($p['title'] ?? '')) ?></h2>
            <p><?= h((string) ($p['body'] ?? '')) ?></p>
        </div>
    </div>
    <?php
}

function xr_block_blog_hero(array $p, string $blockId = ''): void
{
    $img = (string) ($p['image'] ?? '');
    $title = (string) ($p['title'] ?? '');
    $sub = (string) ($p['subtitle'] ?? '');
    ?>
    <div class="xr-blog-hero">
        <div class="xr-blog-hero__media">
            <img src="<?= h($img) ?>" alt="">
            <div class="xr-blog-hero__shade" aria-hidden="true"></div>
        </div>
        <div class="xr-blog-hero__text">
            <h1 class="xr-blog-hero__title xr-reveal"><?= h($title) ?></h1>
            <p class="xr-blog-hero__sub xr-reveal"><?= h($sub) ?></p>
        </div>
    </div>
    <?php
}

function xr_block_blog_masonry(array $p, string $blockId = ''): void
{
    $heading = (string) ($p['heading'] ?? '');
    $pinned = is_array($p['pinned'] ?? null) ? $p['pinned'] : [];
    $posts = is_array($p['posts'] ?? null) ? $p['posts'] : [];
    $anchor = (string) ($pinned['anchor'] ?? '#block-4-3');
    ?>
    <div class="xr-blog-masonry">
        <?php if ($heading !== ''): ?>
            <h2 class="xr-blog-masonry__heading xr-reveal"><?= h($heading) ?></h2>
        <?php endif; ?>
        <div class="xr-blog-masonry__grid">
            <?php if ($pinned !== []): ?>
                <a class="xr-blog-masonry__pinned xr-reveal" href="<?= h($anchor) ?>">
                    <span class="xr-blog-masonry__pin-badge"><?= h((string) ($pinned['badge'] ?? 'Pinned')) ?></span>
                    <img src="<?= h((string) ($pinned['image'] ?? '')) ?>" alt="">
                    <div class="xr-blog-masonry__pinned-body">
                        <h3><?= h((string) ($pinned['title'] ?? '')) ?></h3>
                        <p><?= h((string) ($pinned['excerpt'] ?? '')) ?></p>
                        <span class="xr-blog-masonry__read"><?= h((string) ($pinned['cta'] ?? 'Read full post →')) ?></span>
                    </div>
                </a>
            <?php endif; ?>
            <div class="xr-blog-masonry__columns xr-reveal">
                <?php foreach ($posts as $post): ?>
                    <?php if (!is_array($post)) {
                        continue;
                    } ?>
                    <article class="xr-blog-card">
                        <img src="<?= h((string) ($post['image'] ?? '')) ?>" alt="">
                        <div class="xr-blog-card__body">
                            <h3><?= h((string) ($post['title'] ?? '')) ?></h3>
                            <p><?= h((string) ($post['excerpt'] ?? '')) ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php
}

function xr_block_blog_pinned_detail(array $p, string $blockId = ''): void
{
    ?>
    <article class="xr-blog-detail">
        <span class="xr-blog-detail__badge"><?= h((string) ($p['badge'] ?? 'Pinned')) ?></span>
        <h2 class="xr-blog-detail__title xr-reveal"><?= h((string) ($p['title'] ?? '')) ?></h2>
        <time class="xr-blog-detail__date xr-reveal" datetime=""><?= h((string) ($p['date'] ?? '')) ?></time>
        <div class="xr-blog-detail__hero xr-reveal">
            <img src="<?= h((string) ($p['image'] ?? '')) ?>" alt="">
        </div>
        <div class="xr-blog-detail__body xr-reveal">
            <?php
            $paras = $p['body'] ?? '';
            if (is_string($paras)) {
                foreach (preg_split('/\n\s*\n/', $paras) as $para) {
                    $para = trim($para);
                    if ($para !== '') {
                        echo '<p>' . nl2br(h($para)) . '</p>';
                    }
                }
            }
            ?>
        </div>
        <p class="xr-blog-detail__back xr-reveal">
            <a href="<?= h((string) ($p['back_href'] ?? '/#block-1-9-10')) ?>"><?= h((string) ($p['back_label'] ?? '← Back')) ?></a>
        </p>
    </article>
    <?php
}
