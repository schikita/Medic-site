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
    $poster   = (string) ($p['poster'] ?? '/assets/img/hero-bg.png');
    $mp4      = (string) ($p['video_mp4'] ?? '');
    $webm     = (string) ($p['video_webm'] ?? '');
    $ytId     = trim((string) ($p['youtube_id'] ?? ''));

    // Auto-extract YouTube ID from mp4 field if someone pasted a YouTube URL
    if ($ytId === '' && $mp4 !== '') {
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $mp4, $m)) {
            $ytId = $m[1];
            $mp4  = '';
        }
    }
    ?>
    <div class="xr-hero-full">
        <div class="xr-hero-full__media">
            <?php if ($ytId !== ''): ?>
                <div class="xr-hero-full__yt-bg" aria-hidden="true">
                    <iframe class="xr-hero-full__yt-iframe"
                        src="https://www.youtube-nocookie.com/embed/<?= h($ytId) ?>?autoplay=1&mute=1&loop=1&playlist=<?= h($ytId) ?>&controls=0&showinfo=0&rel=0&disablekb=1&modestbranding=1&iv_load_policy=3&enablejsapi=1"
                        allow="autoplay; encrypted-media"
                        tabindex="-1"
                        aria-hidden="true"></iframe>
                </div>
                <?php if ($poster !== ''): ?>
                    <img class="xr-hero-full__poster xr-hero-full__poster--yt-fallback" src="<?= h($poster) ?>" alt="">
                <?php endif; ?>
            <?php elseif ($mp4 !== '' || $webm !== ''): ?>
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
    $eyebrow = trim((string) ($p['eyebrow'] ?? ''));
    $body = trim((string) ($p['body'] ?? ''));
    if ($body === '') {
        $body = trim((string) ($p['tagline'] ?? ''));
    }
    $headline = trim((string) ($p['headline'] ?? ''));
    if ($headline === '') {
        $headline = trim(
            trim((string) ($p['headline_line1'] ?? '')) . ' ' . trim((string) ($p['headline_line2'] ?? ''))
        );
    }
    ?>
    <div class="xr-intro-gradient">
        <div class="xr-page-grid xr-intro-gradient__grid">
            <?php if ($eyebrow !== ''): ?>
                <p class="xr-intro-gradient__eyebrow xr-reveal"><?= h($eyebrow) ?></p>
            <?php endif; ?>
            <?php if ($headline !== ''): ?>
                <h2 class="xr-intro-gradient__headline">
                    <span class="xr-intro-gradient__line xr-reveal"><?= h($headline) ?></span>
                </h2>
            <?php endif; ?>
            <?php if ($body !== ''): ?>
                <p class="xr-intro-gradient__body xr-reveal"><?= h($body) ?></p>
            <?php endif; ?>
        </div>
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
    $defaultHeadline = trim((string) ($p['headline'] ?? ''));
    $defaultBadges = is_array($p['badges'] ?? null) ? $p['badges'] : [];
    ?>
    <div class="xr-wave-slider" data-carousel data-interval="<?= (int) $interval ?>">
        <div class="xr-wave-slider__wave" aria-hidden="true">
            <svg class="xr-wave-slider__wave-svg xr-wave-slider__wave-svg--glow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 100" preserveAspectRatio="none">
                <defs>
                    <linearGradient id="waveGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                        <stop offset="23%"  stop-color="rgb(103,205,249)"/>
                        <stop offset="48%"  stop-color="rgb(211,128,228)"/>
                        <stop offset="67%"  stop-color="rgb(248,96,215)"/>
                        <stop offset="82%"  stop-color="rgb(233,86,231)"/>
                        <stop offset="97%"  stop-color="rgb(186,76,250)"/>
                    </linearGradient>
                </defs>
                <path d="M0,10 C240,100 480,100 720,52 C960,4 1200,4 1440,72 L1440,0 L0,0 Z" fill="url(#waveGrad)" opacity="0.55"/>
            </svg>
            <svg class="xr-wave-slider__wave-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 80" preserveAspectRatio="none">
                <path d="M0,0 C240,80 480,80 720,40 C960,0 1200,0 1440,60 L1440,0 L0,0 Z" fill="#ffffff"/>
            </svg>
        </div>
        <div class="xr-wave-slider__viewport">
            <?php foreach ($slides as $i => $s): ?>
                <?php if (!is_array($s)) {
                    continue;
                } ?>
                <?php
                $slideBadges = is_array($s['badges'] ?? null) ? $s['badges'] : $defaultBadges;
                $slideHeadline = trim((string) ($s['headline'] ?? ''));
                if ($slideHeadline === '') {
                    $slideHeadline = $defaultHeadline;
                }
                $sub = trim((string) ($s['subheadline'] ?? ''));
                if ($sub === '') {
                    $sub = trim((string) ($s['caption'] ?? ''));
                }
                ?>
                <figure class="xr-wave-slider__slide<?= $i === 0 ? ' is-active' : '' ?>" data-carousel-slide>
                    <div class="xr-wave-slider__slide-inner">
                        <?php if ($slideHeadline !== '' || $sub !== '' || $slideBadges !== []): ?>
                            <div class="xr-wave-slider__slide-top">
                                <?php if ($slideBadges !== []): ?>
                                    <div class="xr-wave-slider__badges" role="group" aria-label="Tags">
                                        <?php foreach ($slideBadges as $badge): ?>
                                            <?php
                                            $badge = trim((string) $badge);
                                            if ($badge === '') {
                                                continue;
                                            }
                                            ?>
                                            <span class="xr-wave-slider__badge"><?= h($badge) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                <?php if ($slideHeadline !== ''): ?>
                                    <h3 class="xr-wave-slider__slide-title"><?= h($slideHeadline) ?></h3>
                                <?php endif; ?>
                                <?php if ($sub !== ''): ?>
                                    <p class="xr-wave-slider__slide-sub"><?= h($sub) ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <div class="xr-wave-slider__slide-media">
                            <img src="<?= h((string) ($s['image'] ?? '')) ?>" alt="">
                        </div>
                    </div>
                </figure>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

function xr_block_layered_star(array $p, string $blockId = ''): void
{
    $title = (string) ($p['title'] ?? '');
    $titleLine2 = trim((string) ($p['title_line2'] ?? ''));
    $subtitle = (string) ($p['subtitle'] ?? '');
    $base = (string) ($p['base_color'] ?? '#151a22');
    $showDownWave = !isset($p['down_wave']) || (bool) $p['down_wave'];
    ?>
    <div class="xr-layered-star" style="--xr-layer-base: <?= h($base) ?>">
        <div class="xr-layered-star__bg" aria-hidden="true"></div>
        <canvas class="xr-layered-star__canvas" data-starfield width="800" height="500" aria-hidden="true"></canvas>
        <div class="xr-layered-star__gradient" aria-hidden="true"></div>
        <?php if ($showDownWave): ?>
        <div class="xr-layered-star__wave" aria-hidden="true">
            <svg class="xr-layered-star__wave-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 80" preserveAspectRatio="none">
                <path d="M0,0 L1440,0 L1440,24 C1200,80 960,80 720,44 C480,8 240,8 0,64 Z" fill="#0c0f14"/>
            </svg>
        </div>
        <?php endif; ?>
        <div class="xr-layered-star__edge-glow" aria-hidden="true"></div>
        <div class="xr-layered-star__content">
            <h2 class="xr-layered-star__title xr-burst-text">
                <span class="xr-layered-star__title-line"><?= h($title) ?></span>
                <?php if ($titleLine2 !== ''): ?>
                    <span class="xr-layered-star__title-line"><?= h($titleLine2) ?></span>
                <?php endif; ?>
            </h2>
            <p class="xr-layered-star__subtitle xr-burst-text"><?= h($subtitle) ?></p>
        </div>
    </div>
    <?php
}

/**
 * Текст и Play в «линзе» Oculus: SEE + Your Edge Appear (побуквенно) + треугольник без круга.
 */

/** Путь шлема (коорд. SVG; viewBox обрезан ~по bbox контура) — совпадает с clipPath */
function xr_oculus_headset_path_d(): string
{
    return 'M 148 182 Q 138 108, 204 88 Q 263 70, 340 73 Q 417 70, 476 88 Q 542 108, 532 182 Q 542 256, 484 284 Q 456 298, 428 291 Q 408 285, 398 272 Q 376 253, 340 251 Q 304 253, 282 272 Q 272 285, 252 291 Q 224 298, 196 284 Q 138 256, 148 182 Z';
}

function xr_oculus_lens_overlay_play(string $playAria, bool $isMp4, string $youtubeId = ''): void
{
    ?>
    <p class="xr-tabs-media__overlay-line1 xr-tabs-media__overlay-line1--oculus">SEE</p>
    <p class="xr-tabs-media__overlay-line2 xr-tabs-media__overlay-line2--oculus">
        <span class="xr-tabs-media__och xr-tabs-media__och--cyan">Y</span><span class="xr-tabs-media__och xr-tabs-media__och--white">our</span>&nbsp;<span class="xr-tabs-media__och xr-tabs-media__och--pink">E</span><span class="xr-tabs-media__och xr-tabs-media__och--white">dge</span>&nbsp;<span class="xr-tabs-media__och xr-tabs-media__och--pink">A</span><span class="xr-tabs-media__och xr-tabs-media__och--white">ppear</span>
    </p>
    <?php if ($isMp4): ?>
        <button type="button" class="xr-tabs-media__play-plain" data-oculus-play=""
                aria-label="<?= h($playAria) ?>">
            <span class="xr-tabs-media__play-plain-icon" aria-hidden="true"></span>
        </button>
    <?php else: ?>
        <button type="button" class="xr-tabs-media__play-plain" data-youtube-load="<?= h($youtubeId) ?>"
                aria-label="<?= h($playAria) ?>">
            <span class="xr-tabs-media__play-plain-icon" aria-hidden="true"></span>
        </button>
    <?php endif; ?>
    <?php
}

function xr_block_tabs_youtube_loop(array $p, string $blockId = ''): void
{
    $heading = (string) ($p['heading'] ?? '');
    $subheading = trim((string) ($p['subheading'] ?? ''));
    $tabs = is_array($p['tabs'] ?? null) ? $p['tabs'] : [];
    $headingLg = ((string) ($p['heading_size'] ?? '')) === 'lg';
    $splitLayout = (string) ($p['layout'] ?? '') === 'split';
    $rootClass = 'xr-tabs-media' . ($headingLg ? ' xr-tabs-media--heading-lg' : '') . ($splitLayout ? ' xr-tabs-media--split' : '');
    ?>
    <div class="<?= h($rootClass) ?>"<?= $splitLayout ? ' data-xr-tabs-oculus="1"' : '' ?>>
        <?php if ($splitLayout && ($heading !== '' || $subheading !== '')): ?>
            <div class="xr-tabs-media__intro">
                <?php if ($heading !== ''): ?>
                    <h2 class="xr-tabs-media__heading xr-tabs-media__heading--gradient"><?= h($heading) ?></h2>
                <?php endif; ?>
                <?php if ($subheading !== ''): ?>
                    <p class="xr-tabs-media__sub"><?= h($subheading) ?></p>
                <?php endif; ?>
            </div>
        <?php elseif ($heading !== ''): ?>
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
        <?php if ($splitLayout): ?>
            <div class="xr-tabs-media__split xr-tabs-media__split--oculus">
                <div class="xr-tabs-media__split-grid">
                    <div class="xr-tabs-media__split-col xr-tabs-media__split-col--copy">
                        <?php foreach ($tabs as $i => $t): ?>
                            <?php if (!is_array($t)) {
                                continue;
                            } ?>
                            <?php
                            $badge = trim((string) ($t['badge'] ?? ''));
                            $panelTitle = trim((string) ($t['panel_title'] ?? ''));
                            $body = trim((string) ($t['body'] ?? ''));
                            ?>
                            <div class="xr-tabs-media__panel<?= $i === 0 ? ' is-active' : '' ?>" role="tabpanel"
                                 id="xr-tm-panel-<?= (int) $i ?>"
                                 aria-labelledby="xr-tm-tab-<?= (int) $i ?>"
                                 data-xr-panel="media"
                                 data-index="<?= (int) $i ?>">
                                <div class="xr-tabs-media__copy">
                                    <?php if ($badge !== ''): ?>
                                        <span class="xr-tabs-media__badge"><?= h($badge) ?></span>
                                    <?php endif; ?>
                                    <?php if ($panelTitle !== ''): ?>
                                        <h3 class="xr-tabs-media__panel-title"><?= h($panelTitle) ?></h3>
                                    <?php endif; ?>
                                    <?php if ($body !== ''): ?>
                                        <p class="xr-tabs-media__body"><?= h($body) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="xr-tabs-media__split-col xr-tabs-media__split-col--device">
                        <div class="xr-tabs-media__oculus">
                            <div class="xr-tabs-media__oculus-inner">
                                <?php foreach ($tabs as $i => $t): ?>
                                    <?php if (!is_array($t)) {
                                        continue;
                                    } ?>
                                    <?php
                                    $mode = (string) ($t['mode'] ?? 'youtube_click');
                                    $poster = (string) ($t['poster'] ?? '');
                                    $yt = (string) ($t['youtube_id'] ?? '');
                                    $mp4 = (string) ($t['mp4'] ?? '');
                                    $playLabel = (string) ($t['play_label'] ?? 'Play');
                                    $playAria = $playLabel !== '' ? $playLabel : 'Play video';
                                    ?>
                                    <?php
                                    $bidSafe = preg_replace('/[^a-zA-Z0-9_-]/', '', $blockId);
                                    if ($bidSafe === '') {
                                        $bidSafe = 'media';
                                    }
                                    $clipId = 'xr-headset-clip-' . $bidSafe . '-' . (int) $i;
                                    $headsetD = h(xr_oculus_headset_path_d());
                                    ?>
                                    <div class="xr-tabs-media__oculus-slot<?= $i === 0 ? ' is-active' : '' ?>"
                                         data-oculus-slot="<?= (int) $i ?>"
                                         data-index="<?= (int) $i ?>">
                                        <svg class="xr-headset-slot-svg" viewBox="133 63 414 240" preserveAspectRatio="xMidYMid meet" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true">
                                            <defs>
                                                <clipPath id="<?= h($clipId) ?>">
                                                    <path d="<?= $headsetD ?>"/>
                                                </clipPath>
                                            </defs>
                                            <g clip-path="url(#<?= h($clipId) ?>)">
                                                <foreignObject x="148" y="70" width="384" height="232">
                                                    <div xmlns="http://www.w3.org/1999/xhtml" class="xr-headset-fo">
                                                        <div class="xr-headset-fo-body">
                                                            <?php if ($mode === 'youtube_click'): ?>
                                                                <div class="xr-tabs-media__vision-screen">
                                                                    <div class="xr-yt-mask">
                                                                        <img src="<?= h($poster) ?>" alt="">
                                                                        <div class="xr-tabs-media__poster-overlay xr-tabs-media__poster-overlay--oculus">
                                                                            <?php xr_oculus_lens_overlay_play($playAria, false, $yt); ?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="xr-yt-frame" hidden data-youtube-frame></div>
                                                                </div>
                                                            <?php elseif ($mode === 'video_loop'): ?>
                                                                <div class="xr-tabs-media__vision-screen">
                                                                    <video class="xr-oculus-video" muted playsinline loop preload="metadata" poster="<?= h($poster) ?>">
                                                                        <?php if ($mp4 !== ''): ?><source src="<?= h($mp4) ?>" type="video/mp4"><?php endif; ?>
                                                                    </video>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </foreignObject>
                                            </g>
                                            <path class="xr-headset-chrome" d="<?= $headsetD ?>" fill="none" stroke="#4a6db5" stroke-width="2.5"/>
                                            <path class="xr-headset-chrome xr-headset-chrome--highlight" d="M 210 84 Q 340 68, 470 84" fill="none" stroke="#6080c0" stroke-width="1" opacity="0.5" stroke-linecap="round"/>
                                        </svg>
                                        <?php if ($mode === 'video_loop'): ?>
                                            <div class="xr-tabs-media__vision-overlay xr-tabs-media__poster-overlay--oculus" data-oculus-overlay>
                                                <?php xr_oculus_lens_overlay_play($playAria, true); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($tabs as $i => $t): ?>
                <?php if (!is_array($t)) {
                    continue;
                } ?>
                <?php
                $mode = (string) ($t['mode'] ?? 'youtube_click');
                $poster = (string) ($t['poster'] ?? '');
                $yt = (string) ($t['youtube_id'] ?? '');
                $mp4 = (string) ($t['mp4'] ?? '');
                $badge = trim((string) ($t['badge'] ?? ''));
                $panelTitle = trim((string) ($t['panel_title'] ?? ''));
                $body = trim((string) ($t['body'] ?? ''));
                $ol1 = trim((string) ($t['overlay_line1'] ?? ''));
                $ol2 = trim((string) ($t['overlay_line2'] ?? ''));
                $playLabel = (string) ($t['play_label'] ?? 'Play');
                $playAria = $playLabel !== '' ? $playLabel : 'Play video';
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
                                <button type="button" class="xr-yt-play btn btn--gradient" data-youtube-load="<?= h($yt) ?>"><?= h($playLabel !== '' ? $playLabel : 'Play') ?></button>
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
        <?php endif; ?>
    </div>
    <?php
}

function xr_block_video_freeze_section(array $p, string $blockId = ''): void
{
    $mp4        = (string) ($p['mp4'] ?? '');
    $youtubeId  = trim((string) ($p['youtube_id'] ?? ''));
    $poster     = (string) ($p['poster'] ?? '');
    $caption    = (string) ($p['caption'] ?? '');
    $heading    = trim((string) ($p['heading'] ?? ''));
    $headingLine2 = trim((string) ($p['heading_line2'] ?? ''));
    $intro      = trim((string) ($p['intro'] ?? ''));

    // Auto-extract YouTube ID from full URL if needed
    if ($youtubeId === '' && $mp4 !== '') {
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $mp4, $m)) {
            $youtubeId = $m[1];
            $mp4 = '';
        }
    }

    if ($mp4 === '' && $youtubeId === '') {
        return;
    }
    ?>
    <div class="xr-video-freeze">
        <?php if ($heading !== '' || $headingLine2 !== '' || $intro !== ''): ?>
            <header class="xr-video-freeze__head">
                <?php if ($heading !== '' || $headingLine2 !== ''): ?>
                    <h2 class="xr-video-freeze__title">
                        <?php if ($heading !== ''): ?>
                            <span class="xr-video-freeze__title-line"><?= h($heading) ?></span>
                        <?php endif; ?>
                        <?php if ($headingLine2 !== ''): ?>
                            <span class="xr-video-freeze__title-line"><?= h($headingLine2) ?></span>
                        <?php endif; ?>
                    </h2>
                <?php endif; ?>
                <?php if ($intro !== ''): ?>
                    <p class="xr-video-freeze__intro"><?= h($intro) ?></p>
                <?php endif; ?>
            </header>
        <?php endif; ?>

        <?php if ($youtubeId !== ''): ?>
            <div class="xr-video-freeze__yt-wrap">
                <iframe class="xr-video-freeze__yt"
                    src="https://www.youtube-nocookie.com/embed/<?= h($youtubeId) ?>?autoplay=1&mute=1&controls=1&loop=1&playlist=<?= h($youtubeId) ?>&rel=0&modestbranding=1"
                    title="<?= h($heading ?: 'Video') ?>"
                    allow="autoplay; encrypted-media; fullscreen"
                    allowfullscreen
                    loading="lazy"></iframe>
            </div>
        <?php else: ?>
            <video class="xr-video-freeze__video" muted playsinline data-video-freeze poster="<?= h($poster) ?>">
                <source src="<?= h($mp4) ?>" type="video/mp4">
            </video>
        <?php endif; ?>

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
    $tabs   = is_array($p['tabs'] ?? null) ? $p['tabs'] : [];
    $panels = is_array($p['panels'] ?? null) ? $p['panels'] : [];
    $active = (int) ($p['active_tab'] ?? 0);
    $layout = (string) ($p['layout'] ?? '');

    /* ——— Slider layout ——— */
    if ($layout === 'slider') {
        $panel0   = is_array($panels[0] ?? null) ? $panels[0] : [];
        $panel1   = is_array($panels[1] ?? null) ? $panels[1] : [];
        $panel2   = is_array($panels[2] ?? null) ? $panels[2] : [];
        $scBadge  = trim((string) ($p['showcase_badge'] ?? ''));
        $scHead   = trim((string) ($p['showcase_heading'] ?? ''));
        $scItems  = is_array($p['showcase_items'] ?? null) ? $p['showcase_items'] : [];
        $vidPoster = trim((string) ($panel0['poster'] ?? ''));
        $vidYtId  = trim((string) ($panel0['youtube_id'] ?? ''));
        $vidLabel = trim((string) ($panel0['video_label'] ?? "See\nXR Doctor Platform\nin Action"));
        $slideCards = [];
        foreach ([$panel0, $panel1] as $pnl) {
            $slideCards[] = [
                'title'    => trim((string) ($pnl['title'] ?? '')),
                'image'    => trim((string) ($pnl['card_image'] ?? $pnl['poster'] ?? '')),
                'features' => is_array($pnl['card_features'] ?? null) ? $pnl['card_features'] : (is_array($pnl['features'] ?? null) ? $pnl['features'] : []),
            ];
        }
        $fg = is_array($panel2['feature_grid'] ?? null) ? $panel2['feature_grid'] : [];
        ?>
        <div class="xr-pt-slider" data-pt-slider>
            <div class="xr-pt-slider__body">
                <!-- Left column -->
                <div class="xr-pt-slider__left">
                    <?php if ($scBadge !== ''): ?>
                        <span class="xr-pt-slider__badge"><?= h($scBadge) ?></span>
                    <?php endif; ?>
                    <?php if ($scHead !== ''): ?>
                        <h2 class="xr-pt-slider__heading">
                            <?php foreach (explode("\n", $scHead) as $hl): ?>
                                <span><?= h(trim($hl)) ?></span>
                            <?php endforeach; ?>
                        </h2>
                    <?php endif; ?>
                    <?php if ($scItems !== []): ?>
                        <ul class="xr-pt-slider__nav" role="list">
                            <?php foreach ($scItems as $si => $sit): ?>
                                <?php if (!is_array($sit)) {
                                    continue;
                                } ?>
                                <li>
                                    <button type="button"
                                            class="xr-pt-slider__nav-link<?= $si === 0 ? ' is-active' : '' ?>"
                                            data-pt-nav="<?= (int) $si ?>"><?= h((string) ($sit['label'] ?? '')) ?></button>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <!-- Right: slides -->
                <div class="xr-pt-slider__right">
                    <div class="xr-pt-slider__viewport">
                        <div class="xr-pt-slider__track" data-pt-track>

                            <!-- Slide 0: video -->
                            <div class="xr-pt-slider__slide xr-pt-slider__slide--video">
                                <div class="xr-pt-slider__video-stage">
                                    <div class="xr-yt-mask">
                                        <?php if ($vidPoster !== ''): ?>
                                            <img src="<?= h($vidPoster) ?>" alt="">
                                        <?php endif; ?>
                                        <div class="xr-pt-slider__video-overlay">
                                            <?php if ($vidLabel !== ''): ?>
                                                <p class="xr-pt-slider__video-label"><?= nl2br(h($vidLabel)) ?></p>
                                            <?php endif; ?>
                                            <?php if ($vidYtId !== ''): ?>
                                                <button type="button" class="xr-pt-slider__play-btn"
                                                        data-youtube-load="<?= h($vidYtId) ?>"
                                                        aria-label="Play video">
                                                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true"><path d="M4 2L12 7L4 12V2Z" fill="white"/></svg>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="xr-yt-frame" hidden data-youtube-frame></div>
                                </div>
                            </div>

                            <!-- Slide 1: two product cards -->
                            <div class="xr-pt-slider__slide xr-pt-slider__slide--cards">
                                <div class="xr-pt-slider__cards">
                                    <?php foreach ($slideCards as $sci => $sc): ?>
                                        <div class="xr-pt-slider__card xr-pt-slider__card--<?= (int) $sci ?>">
                                            <?php if ($sc['image'] !== ''): ?>
                                                <div class="xr-pt-slider__card-img">
                                                    <img src="<?= h($sc['image']) ?>" alt="">
                                                </div>
                                            <?php endif; ?>
                                            <div class="xr-pt-slider__card-body">
                                                <h3 class="xr-pt-slider__card-title"><?= h($sc['title']) ?></h3>
                                                <?php if ($sc['features'] !== []): ?>
                                                    <ul class="xr-pt-slider__card-list">
                                                        <?php foreach ($sc['features'] as $f): ?>
                                                            <li><?= h((string) (is_array($f) ? ($f['label'] ?? '') : $f)) ?></li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Slide 2: AI feature grid -->
                            <div class="xr-pt-slider__slide xr-pt-slider__slide--grid">
                                <div class="xr-pt-slider__grid">
                                    <?php foreach ($fg as $fi): ?>
                                        <?php if (!is_array($fi)) {
                                            continue;
                                        } ?>
                                        <?php $fcolor = h((string) ($fi['icon_color'] ?? 'cyan')); ?>
                                        <div class="xr-pt-slider__grid-item">
                                            <div class="xr-pt-slider__grid-hd">
                                                <span class="xr-pt-slider__grid-icon xr-pt-slider__grid-icon--<?= $fcolor ?>">
                                                    <?php $iimg = trim((string) ($fi['icon_img'] ?? '')); ?>
                                                    <?php if ($iimg !== ''): ?>
                                                        <img src="<?= h($iimg) ?>" alt="">
                                                    <?php else: ?>
                                                        <?= h((string) ($fi['icon_emoji'] ?? '◉')) ?>
                                                    <?php endif; ?>
                                                </span>
                                                <strong class="xr-pt-slider__grid-title"><?= h((string) ($fi['title'] ?? '')) ?></strong>
                                            </div>
                                            <p class="xr-pt-slider__grid-desc"><?= h((string) ($fi['body'] ?? '')) ?></p>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Arrows -->
                    <button type="button" class="xr-pt-slider__arrow xr-pt-slider__arrow--prev" data-pt-prev aria-label="Previous slide">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M10 12L6 8L10 4" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    <button type="button" class="xr-pt-slider__arrow xr-pt-slider__arrow--next" data-pt-next aria-label="Next slide">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M6 4L10 8L6 12" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>
            </div>

            <!-- Dots -->
            <div class="xr-pt-slider__dots">
                <?php for ($di = 0; $di < 3; $di++): ?>
                    <button type="button"
                            class="xr-pt-slider__dot<?= $di === 0 ? ' is-active' : '' ?>"
                            data-pt-dot="<?= $di ?>"
                            aria-label="Slide <?= $di + 1 ?>"></button>
                <?php endfor; ?>
            </div>
        </div>
        <?php
        return;
    }

    /* ——— Showcase layout ——— */
    if ($layout === 'showcase') {
        $badge   = trim((string) ($p['showcase_badge'] ?? ''));
        $heading = trim((string) ($p['showcase_heading'] ?? ''));
        $items   = is_array($p['showcase_items'] ?? null) ? $p['showcase_items'] : [];
        $n       = count($panels);
        ?>
        <div class="xr-product-tabs xr-product-tabs--showcase" data-showcase-tabs>
            <div class="xr-product-tabs__bar" role="tablist">
                <?php foreach ($tabs as $i => $label): ?>
                    <?php $hasGrid = is_array($panels[$i]['feature_grid'] ?? null) && $panels[$i]['feature_grid'] !== []; ?>
                    <button type="button" role="tab"
                            class="xr-product-tabs__tab<?= $i === $active ? ' is-active' : '' ?>"
                            data-xr-tab="product-sc" data-index="<?= (int) $i ?>"
                            <?= $hasGrid ? 'data-sc-feature="1"' : '' ?>
                            aria-selected="<?= $i === $active ? 'true' : 'false' ?>"><?= h((string) $label) ?></button>
                <?php endforeach; ?>
            </div>
            <div class="xr-product-tabs__sc-wrap">
                <div class="xr-product-tabs__sc-intro">
                    <?php if ($badge !== ''): ?>
                        <span class="xr-product-tabs__sc-badge"><?= h($badge) ?></span>
                    <?php endif; ?>
                    <?php if ($heading !== ''): ?>
                        <h2 class="xr-product-tabs__sc-heading">
                            <?php foreach (explode("\n", $heading) as $line): ?>
                                <span><?= h(trim($line)) ?></span>
                            <?php endforeach; ?>
                        </h2>
                    <?php endif; ?>
                    <?php if ($items !== []): ?>
                        <ul class="xr-product-tabs__sc-items">
                            <?php foreach ($items as $it): ?>
                                <?php if (!is_array($it)) {
                                    continue;
                                } ?>
                                <li class="xr-product-tabs__sc-item<?= !empty($it['underline']) ? ' xr-product-tabs__sc-item--ul' : '' ?>">
                                    <?= h((string) ($it['label'] ?? '')) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <?php
                /* Does the INITIAL active tab have a feature_grid? */
                $activeHasGrid = is_array($panels[$active]['feature_grid'] ?? null) && $panels[$active]['feature_grid'] !== [];
                ?>

                <!-- Photo-card pair (hidden when active tab has feature_grid) -->
                <div class="xr-product-tabs__sc-cards" data-sc-cards data-sc-total="<?= (int) $n ?>"
                     <?= $activeHasGrid ? 'hidden' : '' ?>>
                    <?php foreach ($panels as $i => $panel): ?>
                        <?php if (!is_array($panel)) {
                            continue;
                        } ?>
                        <?php
                        $leftIdx  = 0;
                        $rightIdx = min($n - 1, 1);
                        $visible  = $i === $leftIdx || $i === $rightIdx;
                        ?>
                        <div class="xr-product-tabs__sc-card<?= $visible ? ' is-visible' : '' ?><?= $i === $active ? ' is-active' : '' ?>"
                             data-sc-card="<?= (int) $i ?>">
                            <div class="xr-product-tabs__sc-card-img">
                                <?php $ci = trim((string) ($panel['card_image'] ?? $panel['poster'] ?? '')); ?>
                                <?php if ($ci !== ''): ?>
                                    <img src="<?= h($ci) ?>" alt="">
                                <?php endif; ?>
                            </div>
                            <div class="xr-product-tabs__sc-card-body">
                                <h3 class="xr-product-tabs__sc-card-title"><?= h((string) ($panel['title'] ?? '')) ?></h3>
                                <?php
                                $cf = is_array($panel['card_features'] ?? null) ? $panel['card_features'] : (is_array($panel['features'] ?? null) ? $panel['features'] : []);
                                if ($cf !== []):
                                ?>
                                <ul class="xr-product-tabs__sc-card-feats">
                                    <?php foreach ($cf as $f): ?>
                                        <li><?= h((string) (is_array($f) ? ($f['label'] ?? '') : $f)) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Feature-grid panels (one per tab that has feature_grid, hidden unless active) -->
                <?php foreach ($panels as $i => $panel): ?>
                    <?php if (!is_array($panel)) {
                        continue;
                    } ?>
                    <?php $fg = is_array($panel['feature_grid'] ?? null) ? $panel['feature_grid'] : []; ?>
                    <?php if ($fg === []) {
                        continue;
                    } ?>
                    <div class="xr-product-tabs__sc-feature-panel" data-sc-feature-panel="<?= (int) $i ?>"
                         <?= $i !== $active ? 'hidden' : '' ?>>
                        <div class="xr-product-tabs__sc-grid">
                            <?php foreach ($fg as $fc): ?>
                                <?php if (!is_array($fc)) {
                                    continue;
                                } ?>
                                <?php $color = (string) ($fc['icon_color'] ?? 'cyan'); ?>
                                <div class="xr-product-tabs__sc-fcard">
                                    <div class="xr-product-tabs__sc-fcard-icon xr-product-tabs__sc-fcard-icon--<?= h($color) ?>">
                                        <?php $iconImg = trim((string) ($fc['icon_img'] ?? '')); ?>
                                        <?php if ($iconImg !== ''): ?>
                                            <img src="<?= h($iconImg) ?>" alt="">
                                        <?php else: ?>
                                            <span aria-hidden="true">◉</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="xr-product-tabs__sc-fcard-text">
                                        <h4 class="xr-product-tabs__sc-fcard-title"><?= h((string) ($fc['title'] ?? '')) ?></h4>
                                        <p class="xr-product-tabs__sc-fcard-body"><?= h((string) ($fc['body'] ?? '')) ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>
        </div>
        <?php
        return;
    }

    /* ——— Default / split-video layout ——— */
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
            <?php
            $ytId     = trim((string) ($panel['youtube_id'] ?? ''));
            $poster   = trim((string) ($panel['poster'] ?? ''));
            $vidLabel = trim((string) ($panel['video_label'] ?? ''));
            $hasVideo = $ytId !== '' || $poster !== '';
            ?>
            <div class="xr-product-tabs__panel<?= $i === $active ? ' is-active' : '' ?><?= $hasVideo ? ' xr-product-tabs__panel--with-video' : '' ?>" data-xr-panel="product" data-index="<?= (int) $i ?>">
                <div class="xr-product-tabs__text">
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
                <?php if ($hasVideo): ?>
                <div class="xr-product-tabs__stage">
                    <div class="xr-yt-mask">
                        <?php if ($poster !== ''): ?>
                            <img src="<?= h($poster) ?>" alt="">
                        <?php endif; ?>
                        <div class="xr-product-tabs__play-overlay">
                            <?php if ($vidLabel !== ''): ?>
                                <p class="xr-product-tabs__play-label"><?= nl2br(h($vidLabel)) ?></p>
                            <?php endif; ?>
                            <?php if ($ytId !== ''): ?>
                                <button type="button" class="xr-product-tabs__play-btn" data-youtube-load="<?= h($ytId) ?>" aria-label="Play video">
                                    <span class="xr-product-tabs__play-icon" aria-hidden="true"></span>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="xr-yt-frame" hidden data-youtube-frame></div>
                </div>
                <?php endif; ?>
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

function xr_block_expertise_banner(array $p, string $blockId = ''): void
{
    $title    = (string) ($p['title'] ?? '');
    $subtitle = (string) ($p['subtitle'] ?? '');
    ?>
    <div class="xr-expertise-banner">
        <div class="xr-expertise-banner__particles" aria-hidden="true"></div>
        <div class="xr-expertise-banner__content">
            <?php if ($title !== ''): ?>
                <h2 class="xr-expertise-banner__title"><?= h($title) ?></h2>
            <?php endif; ?>
            <?php if ($subtitle !== ''): ?>
                <p class="xr-expertise-banner__subtitle"><?= h($subtitle) ?></p>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

function xr_block_medical_stats(array $p, string $blockId = ''): void
{
    $eyebrow  = (string) ($p['eyebrow'] ?? '');
    $title    = (string) ($p['title'] ?? '');
    $subtitle = (string) ($p['subtitle'] ?? '');
    $stats    = is_array($p['stats'] ?? null) ? $p['stats'] : [];
    ?>
    <div class="xr-medical-stats">
        <div class="xr-medical-stats__head">
            <?php if ($eyebrow !== ''): ?>
                <p class="xr-medical-stats__eyebrow"><?= h($eyebrow) ?></p>
            <?php endif; ?>
            <?php if ($title !== ''): ?>
                <h2 class="xr-medical-stats__title"><?= h($title) ?></h2>
            <?php endif; ?>
            <?php if ($subtitle !== ''): ?>
                <p class="xr-medical-stats__subtitle"><?= h($subtitle) ?></p>
            <?php endif; ?>
        </div>
        <?php if (!empty($stats)): ?>
            <div class="xr-medical-stats__grid">
                <?php foreach ($stats as $s): if (!is_array($s)) continue; ?>
                    <div class="xr-medical-stats__item">
                        <span class="xr-medical-stats__value"><?= h((string) ($s['value'] ?? '')) ?></span>
                        <strong class="xr-medical-stats__label"><?= h((string) ($s['label'] ?? '')) ?></strong>
                        <?php if (!empty($s['note'])): ?>
                            <span class="xr-medical-stats__note"><?= h((string) $s['note']) ?></span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

function xr_block_journey_timeline(array $p, string $blockId = ''): void
{
    $title    = (string) ($p['title'] ?? '');
    $subtitle = (string) ($p['subtitle'] ?? '');
    $tagline  = (string) ($p['tagline'] ?? '');
    $steps    = is_array($p['steps'] ?? null) ? $p['steps'] : [];
    ?>
    <div class="xr-journey">
        <div class="xr-journey__head">
            <?php if ($title !== ''): ?>
                <h2 class="xr-journey__title"><?= h($title) ?></h2>
            <?php endif; ?>
            <?php if ($subtitle !== ''): ?>
                <p class="xr-journey__subtitle"><?= h($subtitle) ?></p>
            <?php endif; ?>
        </div>
        <?php if (!empty($steps)): ?>
            <div class="xr-journey__track">
                <div class="xr-journey__line" aria-hidden="true"></div>
                <?php foreach ($steps as $s): if (!is_array($s)) continue; ?>
                    <div class="xr-journey__step">
                        <div class="xr-journey__step-top">
                            <strong class="xr-journey__step-title">
                                <?php if (!empty($s['num'])): ?><span class="xr-journey__step-num"><?= h((string) $s['num']) ?>.</span><?php endif; ?>
                                <?= h((string) ($s['title'] ?? '')) ?>
                            </strong>
                        </div>
                        <div class="xr-journey__dot" aria-hidden="true"></div>
                        <p class="xr-journey__step-text"><?= h((string) ($s['text'] ?? '')) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if ($tagline !== ''): ?>
            <p class="xr-journey__tagline"><?= h($tagline) ?></p>
        <?php endif; ?>
    </div>
    <?php
}

function xr_block_requirements_grid(array $p, string $blockId = ''): void
{
    $tags    = is_array($p['tags'] ?? null) ? $p['tags'] : [];
    $title   = (string) ($p['title'] ?? '');
    $devImg  = (string) ($p['device_image'] ?? '');
    $devCap  = (string) ($p['device_caption'] ?? '');
    $columns = is_array($p['columns'] ?? null) ? $p['columns'] : [];
    ?>
    <div class="xr-reqgrid">
        <?php if (!empty($tags)): ?>
            <div class="xr-reqgrid__tags">
                <?php foreach ($tags as $tag): ?>
                    <span class="xr-reqgrid__tag"><?= h((string) $tag) ?></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="xr-reqgrid__hero">
            <h2 class="xr-reqgrid__title"><?= h($title) ?></h2>
            <div class="xr-reqgrid__device">
                <?php if ($devImg !== ''): ?>
                    <img src="<?= h($devImg) ?>" alt="<?= h($devCap) ?>" class="xr-reqgrid__device-img">
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($columns)): ?>
            <div class="xr-reqgrid__cols">
                <?php foreach ($columns as $col): if (!is_array($col)) continue;
                    $color  = (string) ($col['header_color'] ?? 'blue');
                    $groups = is_array($col['groups'] ?? null) ? $col['groups'] : [];
                    ?>
                    <div class="xr-reqgrid__col">
                        <div class="xr-reqgrid__col-header xr-reqgrid__col-header--<?= h($color) ?>">
                            <?= h((string) ($col['header'] ?? '')) ?>
                        </div>
                        <div class="xr-reqgrid__col-body">
                            <?php foreach ($groups as $g): if (!is_array($g)) continue; ?>
                                <div class="xr-reqgrid__group">
                                    <?php if (!empty($g['title'])): ?>
                                        <p class="xr-reqgrid__group-title">
                                            <?php if (!empty($g['icon'])):
                                                    $ico = (string) $g['icon'];
                                                    $isUrl = str_starts_with($ico, '/') || str_starts_with($ico, 'http');
                                                ?>
                                                <span class="xr-reqgrid__group-icon">
                                                    <?php if ($isUrl): ?>
                                                        <img src="<?= h($ico) ?>" alt="" class="xr-reqgrid__group-icon-img">
                                                    <?php else: ?>
                                                        <?= h($ico) ?>
                                                    <?php endif; ?>
                                                </span>
                                            <?php endif; ?>
                                            <?= h((string) $g['title']) ?>
                                        </p>
                                    <?php endif; ?>
                                    <?php if (!empty($g['items'])): ?>
                                        <ul class="xr-reqgrid__list">
                                            <?php foreach ((array) $g['items'] as $item):
                                                if (is_array($item)) {
                                                    $itemText = (string) ($item['text'] ?? '');
                                                    $itemIcon = (string) ($item['icon'] ?? '');
                                                } else {
                                                    $itemText = (string) $item;
                                                    $itemIcon = '';
                                                }
                                                $itemIconIsUrl = str_starts_with($itemIcon, '/') || str_starts_with($itemIcon, 'http');
                                            ?>
                                                <li class="xr-reqgrid__list-item<?= $itemIcon !== '' ? ' xr-reqgrid__list-item--icon' : '' ?>">
                                                    <?php if ($itemIcon !== ''): ?>
                                                        <span class="xr-reqgrid__item-icon">
                                                            <?php if ($itemIconIsUrl): ?>
                                                                <img src="<?= h($itemIcon) ?>" alt="">
                                                            <?php else: ?>
                                                                <?= h($itemIcon) ?>
                                                            <?php endif; ?>
                                                        </span>
                                                    <?php endif; ?>
                                                    <span><?= h($itemText) ?></span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

function xr_block_saves_your(array $p, string $blockId = ''): void
{
    $title = (string) ($p['title'] ?? '');
    $items = is_array($p['items'] ?? null) ? $p['items'] : [];
    ?>
    <div class="xr-saves">
        <?php if ($title !== ''): ?>
            <h2 class="xr-saves__title"><?= h($title) ?></h2>
        <?php endif; ?>
        <div class="xr-saves__grid">
            <?php foreach ($items as $it): if (!is_array($it)) continue; ?>
                <div class="xr-saves__item">
                    <?php if (!empty($it['image'])): ?>
                        <div class="xr-saves__img-wrap">
                            <img src="<?= h((string) $it['image']) ?>" alt="<?= h((string) ($it['label'] ?? '')) ?>" class="xr-saves__img">
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($it['label'])): ?>
                        <p class="xr-saves__label"><?= h((string) $it['label']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($it['text'])): ?>
                        <p class="xr-saves__text"><?= h((string) $it['text']) ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

function xr_block_beginning_banner(array $p, string $blockId = ''): void
{
    $eyebrow  = (string) ($p['eyebrow'] ?? '');
    $title    = (string) ($p['title'] ?? '');
    $subtitle = (string) ($p['subtitle'] ?? '');
    ?>
    <div class="xr-beginning">
        <?php if ($eyebrow !== ''): ?>
            <p class="xr-beginning__eyebrow"><?= h($eyebrow) ?></p>
        <?php endif; ?>
        <?php if ($title !== ''): ?>
            <h2 class="xr-beginning__title"><?= h($title) ?></h2>
        <?php endif; ?>
        <?php if ($subtitle !== ''): ?>
            <p class="xr-beginning__subtitle"><?= h($subtitle) ?></p>
        <?php endif; ?>
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
    $title  = (string) ($p['title'] ?? '');
    $btn    = (string) ($p['button_label'] ?? '');
    $href   = (string) ($p['href'] ?? '#');
    $symSrc = (string) ($p['infinity_image'] ?? '/assets/img/figma/home/infinity.png');
    ?>
    <div class="xr-star-cta">
        <canvas class="xr-star-cta__canvas" data-starfield-cta width="1200" height="420" aria-hidden="true"></canvas>
        <div class="xr-star-cta__sym">
            <img class="xr-inf-img" src="<?= h($symSrc) ?>" alt="∞" width="560" height="280" loading="lazy" decoding="async">
        </div>
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

/**
 * Renders a paragraph that may contain **bold** and *italic* markers.
 * Input is plain text; markers are converted to <strong>/<em> after escaping.
 */
function xr_inline_markup(string $raw): string
{
    $s = htmlspecialchars($raw, ENT_QUOTES, 'UTF-8');
    $s = (string) preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $s);
    $s = (string) preg_replace('/\*(.+?)\*/s', '<em>$1</em>', $s);

    return $s;
}

function xr_block_product_detail_tabs(array $p, string $blockId = ''): void
{
    $tabs     = is_array($p['tabs'] ?? null) ? $p['tabs'] : [];
    $ctaTitle = trim((string) ($p['cta_title'] ?? ''));
    $ctaSub   = trim((string) ($p['cta_sub'] ?? ''));
    $ctaHref  = trim((string) ($p['cta_href'] ?? '#'));
    if ($tabs === []) {
        return;
    }
    ?>
    <div class="xr-dtabs">
        <div class="xr-dtabs__bar" role="tablist">
            <?php foreach ($tabs as $i => $t): ?>
                <?php if (!is_array($t)) {
                    continue;
                } ?>
                <button type="button" role="tab"
                        class="xr-dtabs__btn<?= $i === 0 ? ' is-active' : '' ?>"
                        data-xr-tab="dtab" data-index="<?= (int) $i ?>"
                        aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"><?= h((string) ($t['label'] ?? '')) ?></button>
            <?php endforeach; ?>
        </div>

        <?php foreach ($tabs as $i => $t): ?>
            <?php if (!is_array($t)) {
                continue;
            } ?>
            <?php
            $title    = trim((string) ($t['title'] ?? ''));
            $body     = is_array($t['body'] ?? null) ? $t['body'] : [];
            $subItems = is_array($t['sub_items'] ?? null) ? $t['sub_items'] : [];
            $subConts = array_values(array_map(static fn($s) => (string) (is_array($s) ? ($s['content'] ?? '') : ''), $subItems));
            ?>
            <div class="xr-dtabs__panel<?= $i === 0 ? ' is-active' : '' ?>"
                 data-xr-panel="dtab" data-index="<?= (int) $i ?>">
                <?php if ($title !== ''): ?>
                    <h2 class="xr-dtabs__title"><?= h($title) ?></h2>
                <?php endif; ?>
                <div class="xr-dtabs__body">
                    <?php foreach ($body as $para): ?>
                        <?php $text = is_array($para) ? (string) ($para['text'] ?? '') : (string) $para; ?>
                        <?php if (trim($text) !== ''): ?>
                            <p class="xr-dtabs__para"><?= xr_inline_markup($text) ?></p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <?php if ($subItems !== []): ?>
                    <div class="xr-dtabs__sub"
                         data-sub-items="<?= h((string) json_encode($subConts, JSON_UNESCAPED_UNICODE)) ?>">
                        <nav class="xr-dtabs__subnav" role="tablist" aria-label="Sub-navigation">
                            <?php foreach ($subItems as $si => $s): ?>
                                <?php if (!is_array($s)) {
                                    continue;
                                } ?>
                                <button type="button" role="tab"
                                        class="xr-dtabs__subitem<?= $si === 0 ? ' is-active' : '' ?>"
                                        data-sub-idx="<?= (int) $si ?>"
                                        aria-selected="<?= $si === 0 ? 'true' : 'false' ?>">
                                    <?php $icon = trim((string) ($s['icon'] ?? '')); ?>
                                    <?php if ($icon !== ''): ?>
                                        <span class="xr-dtabs__sub-icon" aria-hidden="true"><?= h($icon) ?></span>
                                    <?php endif; ?>
                                    <?= h((string) ($s['label'] ?? '')) ?>
                                </button>
                            <?php endforeach; ?>
                        </nav>
                        <div class="xr-dtabs__subcontent"><?= h($subConts[0] ?? '') ?></div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <?php if ($ctaTitle !== '' || $ctaSub !== ''): ?>
            <div class="xr-dtabs__cta">
                <?php if ($ctaTitle !== ''): ?>
                    <p class="xr-dtabs__cta-title"><?= h($ctaTitle) ?></p>
                <?php endif; ?>
                <?php if ($ctaSub !== ''): ?>
                    <div class="xr-dtabs__cta-sub">
                        <?= h($ctaSub) ?>
                        <a class="xr-dtabs__cta-arrow" href="<?= h($ctaHref) ?>" aria-label="Go">→</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

function xr_block_hologram_stories(array $p, string $blockId = ''): void
{
    $badge    = trim((string) ($p['badge'] ?? ''));
    $heading  = trim((string) ($p['heading'] ?? ''));
    $subhead  = trim((string) ($p['subheading'] ?? ''));
    $stories  = is_array($p['stories'] ?? null) ? $p['stories'] : [];
    if ($stories === []) {
        return;
    }
    $jsonData = array_values(array_filter(array_map(static function ($s) {
        if (!is_array($s)) {
            return null;
        }

        return [
            'icon'    => (string) ($s['icon'] ?? ''),
            'tags'    => is_array($s['tags'] ?? null) ? array_map('strval', $s['tags']) : [],
            'summary' => (string) ($s['summary'] ?? ''),
            'title'   => (string) ($s['title'] ?? ''),
            'body'    => is_array($s['body'] ?? null) ? array_map('strval', $s['body']) : [(string) ($s['body'] ?? '')],
            'footer'  => (string) ($s['footer'] ?? ''),
        ];
    }, $stories)));
    ?>
    <div class="xr-stories" data-stories-root>
        <div class="xr-stories__head">
            <?php if ($badge !== ''): ?>
                <span class="xr-stories__badge"><?= h($badge) ?></span>
            <?php endif; ?>
            <div class="xr-stories__stars" aria-hidden="true">★★★★★</div>
            <?php if ($heading !== ''): ?>
                <h2 class="xr-stories__title"><?= h($heading) ?></h2>
            <?php endif; ?>
            <?php if ($subhead !== ''): ?>
                <p class="xr-stories__sub"><?= h($subhead) ?></p>
            <?php endif; ?>
        </div>
        <div class="xr-stories__carousel" data-stories-carousel>
            <div class="xr-stories__track" data-stories-track></div>
        </div>
        <div class="xr-stories__modal" data-stories-modal hidden>
            <div class="xr-stories__modal-box">
                <button type="button" class="xr-stories__mclose" data-stories-close aria-label="Close">✕</button>
                <button type="button" class="xr-stories__mnav xr-stories__mnav--prev" data-stories-prev aria-label="Previous">‹</button>
                <button type="button" class="xr-stories__mnav xr-stories__mnav--next" data-stories-next aria-label="Next">›</button>
                <div class="xr-stories__mhead">
                    <div class="xr-stories__micon" data-m-icon></div>
                    <div class="xr-stories__mtag" data-m-tag></div>
                </div>
                <div class="xr-stories__mtitle" data-m-title></div>
                <div class="xr-stories__mbody" data-m-body></div>
                <div class="xr-stories__mfooter" data-m-footer></div>
            </div>
        </div>
        <script type="application/json" data-stories-json><?= json_encode($jsonData, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?></script>
    </div>
    <?php
}

function xr_block_impact_stats(array $p, string $blockId = ''): void
{
    $heading  = trim((string) ($p['heading'] ?? ''));
    $subtitle = trim((string) ($p['subtitle'] ?? ''));
    $stats    = is_array($p['stats'] ?? null) ? $p['stats'] : [];
    ?>
    <div class="xr-impact">
        <div class="xr-impact__inner">
            <?php if ($heading !== ''): ?>
                <h2 class="xr-impact__heading"><?= h($heading) ?></h2>
            <?php endif; ?>
            <?php if ($subtitle !== ''): ?>
                <p class="xr-impact__subtitle"><?= h($subtitle) ?></p>
            <?php endif; ?>
            <?php if ($stats !== []): ?>
                <div class="xr-impact__stats">
                    <?php foreach ($stats as $s):
                        if (!is_array($s)) {
                            continue;
                        }
                        $val   = trim((string) ($s['value'] ?? ''));
                        $label = trim((string) ($s['label'] ?? ''));
                        $note  = trim((string) ($s['note'] ?? ''));
                    ?>
                        <div class="xr-impact__stat">
                            <div class="xr-impact__value" aria-label="<?= h($val) ?>"><?= h($val) ?></div>
                            <?php if ($label !== ''): ?>
                                <div class="xr-impact__label"><?= h($label) ?></div>
                            <?php endif; ?>
                            <?php if ($note !== ''): ?>
                                <div class="xr-impact__note"><?= h($note) ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

function xr_block_clinical_circles(array $p, string $blockId = ''): void
{
    $label    = trim((string) ($p['label'] ?? ''));
    $heading  = trim((string) ($p['heading'] ?? ''));
    $subhead  = trim((string) ($p['subhead'] ?? ''));
    $tagline  = trim((string) ($p['tagline'] ?? ''));
    $circles  = is_array($p['circles'] ?? null) ? $p['circles'] : [];
    ?>
    <div class="xr-clinical">
        <div class="xr-clinical__inner">
            <?php if ($label !== ''): ?>
                <p class="xr-clinical__label"><?= h($label) ?></p>
            <?php endif; ?>
            <?php if ($heading !== ''): ?>
                <h2 class="xr-clinical__heading"><?= h($heading) ?></h2>
            <?php endif; ?>
            <?php if ($subhead !== ''): ?>
                <h3 class="xr-clinical__subhead"><?= h($subhead) ?></h3>
            <?php endif; ?>
            <?php if ($circles !== []): ?>
                <div class="xr-clinical__circles">
                    <?php foreach ($circles as $c):
                        if (!is_array($c)) {
                            continue;
                        }
                        $src = trim((string) ($c['src'] ?? ''));
                        $alt = trim((string) ($c['alt'] ?? ''));
                    ?>
                        <div class="xr-clinical__circle-wrap">
                            <?php if ($src !== ''): ?>
                                <img src="<?= h($src) ?>" alt="<?= h($alt) ?>" width="290" height="290" loading="lazy" decoding="async">
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php if ($tagline !== ''): ?>
                <p class="xr-clinical__tagline"><?= h($tagline) ?></p>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

function xr_block_team_visioners(array $p, string $blockId = ''): void
{
    $cardTitle    = trim((string) ($p['card_title'] ?? ''));
    $cardSub      = trim((string) ($p['card_subtitle'] ?? ''));
    $achievements = is_array($p['achievements'] ?? null) ? $p['achievements'] : [];
    $photos       = is_array($p['photos'] ?? null) ? $p['photos'] : [];
    $heading      = trim((string) ($p['heading'] ?? ''));
    $body         = trim((string) ($p['body'] ?? ''));
    $stats        = is_array($p['stats'] ?? null) ? $p['stats'] : [];
    ?>
    <div class="xr-visioners">
        <div class="xr-visioners__grid">

            <!-- Left: achievement card -->
            <div class="xr-visioners__card">
                <?php if ($cardTitle !== '' || $cardSub !== ''): ?>
                    <div class="xr-visioners__card-head">
                        <?php if ($cardTitle !== ''): ?>
                            <span class="xr-visioners__card-title"><?= h($cardTitle) ?></span>
                        <?php endif; ?>
                        <?php if ($cardSub !== ''): ?>
                            <span class="xr-visioners__card-sub"><?= h($cardSub) ?></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php foreach ($achievements as $a):
                    if (!is_array($a)) {
                        continue;
                    }
                    $aTitle = trim((string) ($a['title'] ?? ''));
                    $aItems = is_array($a['items'] ?? null) ? $a['items'] : [];
                ?>
                    <div class="xr-visioners__achiev">
                        <?php if ($aTitle !== ''): ?>
                            <div class="xr-visioners__achiev-title"><?= h($aTitle) ?></div>
                        <?php endif; ?>
                        <?php foreach ($aItems as $item): ?>
                            <div class="xr-visioners__achiev-item"><?= h((string) $item) ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Middle: stacked photos -->
            <div class="xr-visioners__photos">
                <?php foreach ($photos as $ph):
                    if (!is_array($ph)) {
                        continue;
                    }
                    $src = trim((string) ($ph['src'] ?? ''));
                    $alt = trim((string) ($ph['alt'] ?? ''));
                ?>
                    <div class="xr-visioners__photo">
                        <?php if ($src !== ''): ?>
                            <img src="<?= h($src) ?>" alt="<?= h($alt) ?>" loading="lazy" decoding="async">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Right: heading + body + stats -->
            <div class="xr-visioners__right">
                <?php if ($heading !== ''): ?>
                    <h2 class="xr-visioners__heading"><?= h($heading) ?></h2>
                <?php endif; ?>
                <?php if ($body !== ''): ?>
                    <p class="xr-visioners__body"><?= h($body) ?></p>
                <?php endif; ?>
                <?php if ($stats !== []): ?>
                    <div class="xr-visioners__stats">
                        <?php foreach ($stats as $s):
                            if (!is_array($s)) {
                                continue;
                            }
                        ?>
                            <div class="xr-visioners__stat">
                                <div class="xr-visioners__stat-value"><?= h((string) ($s['value'] ?? '')) ?></div>
                                <div class="xr-visioners__stat-label"><?= h((string) ($s['label'] ?? '')) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>
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

function xr_block_engage_split(array $p, string $blockId = ''): void
{
    $eyebrow      = (string) ($p['eyebrow'] ?? '');
    $eyebrowHref  = (string) ($p['eyebrow_href'] ?? '#');
    $titleRaw     = (string) ($p['title'] ?? '');
    $taglines     = is_array($p['taglines'] ?? null) ? $p['taglines'] : [];
    $cardTitle    = (string) ($p['card_title'] ?? '');
    $cardIcon     = (string) ($p['card_icon'] ?? '');
    $cardYt       = trim((string) ($p['card_youtube_id'] ?? ''));
    $cardMp4      = (string) ($p['card_mp4'] ?? '');
    $cardPoster   = (string) ($p['card_poster'] ?? '');

    // Parse YouTube URL if full URL pasted
    if ($cardYt !== '' && !preg_match('/^[a-zA-Z0-9_-]{11}$/', $cardYt)) {
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $cardYt, $m)) {
            $cardYt = $m[1];
        } else {
            $cardYt = '';
        }
    }

    // Title: split on \n or literal \n for multiline
    $titleLines = array_filter(array_map('trim', preg_split('/\\\\n|\n/', $titleRaw)));
    ?>
    <div class="xr-engage-split">
        <div class="xr-engage-split__left">
            <?php if ($eyebrow !== ''): ?>
                <a class="xr-engage-split__eyebrow" href="<?= h($eyebrowHref) ?>"><?= h($eyebrow) ?></a>
            <?php endif; ?>
            <?php if ($titleLines !== []): ?>
                <h2 class="xr-engage-split__title">
                    <?php foreach ($titleLines as $line): ?>
                        <span><?= h($line) ?></span>
                    <?php endforeach; ?>
                </h2>
            <?php endif; ?>
            <?php if ($taglines !== []): ?>
                <ul class="xr-engage-split__taglines" role="list">
                    <?php foreach ($taglines as $tl): ?>
                        <li><?= h((string) $tl) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <div class="xr-engage-split__right">
            <?php if ($cardIcon !== ''): ?>
                <img class="xr-engage-split__card-icon" src="<?= h($cardIcon) ?>" alt="">
            <?php endif; ?>
            <?php if ($cardTitle !== ''): ?>
                <p class="xr-engage-split__card-title"><?= h($cardTitle) ?></p>
            <?php endif; ?>
            <?php if ($cardYt !== ''): ?>
                <iframe class="xr-engage-split__card-yt"
                    src="https://www.youtube-nocookie.com/embed/<?= h($cardYt) ?>?autoplay=1&mute=1&loop=1&playlist=<?= h($cardYt) ?>&controls=0&rel=0&modestbranding=1"
                    allow="autoplay; encrypted-media"
                    tabindex="-1"
                    aria-hidden="true"></iframe>
            <?php elseif ($cardMp4 !== ''): ?>
                <video class="xr-engage-split__card-video" autoplay muted loop playsinline
                       <?= $cardPoster !== '' ? 'poster="' . h($cardPoster) . '"' : '' ?>>
                    <source src="<?= h($cardMp4) ?>" type="video/mp4">
                </video>
            <?php elseif ($cardPoster !== ''): ?>
                <img class="xr-engage-split__card-poster-img" src="<?= h($cardPoster) ?>" alt="">
            <?php endif; ?>
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

function xr_block_reality_slider(array $p, string $blockId = ''): void
{
    $title = (string) ($p['title'] ?? '');
    $slides = is_array($p['slides'] ?? null) ? $p['slides'] : [];
    $interval = (int) ($p['interval_ms'] ?? 3500);
    ?>
    <div class="xr-reality" data-xr-reality data-interval="<?= (int) max(1200, $interval) ?>">
        <?php if ($title !== ''): ?>
            <h2 class="xr-reality__title"><?= h($title) ?></h2>
        <?php endif; ?>
        <div class="xr-reality__stage">
            <?php foreach ($slides as $i => $s): ?>
                <?php if (!is_array($s)) continue; ?>
                <article class="xr-reality__slide<?= $i === 0 ? ' is-active' : '' ?>" data-slide>
                    <p class="xr-reality__lead">
                        <?php
                        $lead = (string) ($s['lead'] ?? '');
                        $accent = (string) ($s['accent'] ?? '');
                        if ($accent !== '' && str_contains($lead, $accent)) {
                            $parts = explode($accent, $lead, 2);
                            echo h($parts[0]) . '<span class="xr-reality__accent">' . h($accent) . '</span>' . h($parts[1]);
                        } else {
                            echo h($lead);
                        }
                        ?>
                    </p>
                    <?php if ((string)($s['note'] ?? '') !== ''): ?>
                        <?php $spark = (string) ($s['spark_image'] ?? ''); ?>
                        <?php if ($spark !== ''): ?>
                            <img class="xr-reality__spark-img" src="<?= h($spark) ?>" alt="" aria-hidden="true">
                        <?php else: ?>
                            <div class="xr-reality__spark" aria-hidden="true"></div>
                        <?php endif; ?>
                        <p class="xr-reality__note"><?= h((string)($s['note'] ?? '')) ?></p>
                        <div class="xr-reality__line" aria-hidden="true"></div>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
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

function xr_block_equips_compare(array $p, string $blockId = ''): void
{
    $title = (string) ($p['title'] ?? '');
    $subtitle = (string) ($p['subtitle'] ?? '');

    $yt = trim((string) ($p['video_youtube_id'] ?? ''));
    $mp4 = (string) ($p['video_mp4'] ?? '');
    $poster = (string) ($p['video_poster'] ?? '');

    if ($yt !== '' && !preg_match('/^[a-zA-Z0-9_-]{11}$/', $yt)) {
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $yt, $m)) {
            $yt = $m[1];
        } else {
            $yt = '';
        }
    }
    ?>
    <div class="xr-equip">
        <?php if ($title !== ''): ?>
            <h2 class="xr-equip__title"><?= h($title) ?></h2>
        <?php endif; ?>
        <?php if ($subtitle !== ''): ?>
            <p class="xr-equip__subtitle"><?= h($subtitle) ?></p>
        <?php endif; ?>

        <div class="xr-equip__frame" data-equip>
            <?php if ($poster !== ''): ?>
                <img class="xr-equip__poster" src="<?= h($poster) ?>" alt="">
            <?php endif; ?>
            <?php if ($yt !== '' || $mp4 !== ''): ?>
                <button class="xr-equip__play" type="button"
                        aria-label="Play video"
                        data-equip-play
                        data-yt="<?= h($yt) ?>"
                        data-mp4="<?= h($mp4) ?>"
                        data-poster="<?= h($poster) ?>"></button>
                <div class="xr-equip__video" data-equip-video hidden></div>
            <?php endif; ?>
        </div>

        <?php if ((string)($p['footer'] ?? '') !== ''): ?>
            <p class="xr-equip__footer">
                <span class="xr-equip__footer-a"><?= h((string)($p['footer_a'] ?? 'Tailored for')) ?></span>
                <span class="xr-equip__footer-b"><?= h((string)($p['footer_b'] ?? 'Every You')) ?></span>
            </p>
        <?php endif; ?>
    </div>
    <?php
}

function xr_block_growth_cards(array $p, string $blockId = ''): void
{
    $title = (string) ($p['title'] ?? '');
    $cards = is_array($p['cards'] ?? null) ? $p['cards'] : [];
    $footerA = (string) ($p['footer_a'] ?? '');
    $footerB = (string) ($p['footer_b'] ?? '');
    ?>
    <div class="xr-growth">
        <?php if ($title !== ''): ?>
            <h2 class="xr-growth__title"><?= h($title) ?></h2>
        <?php endif; ?>

        <div class="xr-growth__grid">
            <?php foreach ($cards as $c): ?>
                <?php if (!is_array($c)) continue; ?>
                <article class="xr-growth__card">
                    <h3 class="xr-growth__card-title"><?= h((string)($c['title'] ?? '')) ?></h3>
                    <p class="xr-growth__card-text"><?= h((string)($c['text'] ?? '')) ?></p>
                </article>
            <?php endforeach; ?>
        </div>

        <?php if ($footerA !== '' || $footerB !== ''): ?>
            <p class="xr-growth__footer">
                <span class="xr-growth__footer-a"><?= h($footerA) ?></span>
                <span class="xr-growth__footer-b"><?= h($footerB) ?></span>
            </p>
        <?php endif; ?>
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

function xr_block_assistant_tabs_slider(array $p, string $blockId = ''): void
{
    $title = (string)($p['title'] ?? '');
    $tabs = is_array($p['tabs'] ?? null) ? $p['tabs'] : [];
    $footerPrefix = (string)($p['footer_prefix'] ?? '');
    ?>
    <div class="xr-assist" data-assist-tabs>
        <?php if ($title !== ''): ?>
            <h2 class="xr-assist__title"><?= h($title) ?></h2>
        <?php endif; ?>

        <div class="xr-assist__tabbar" role="tablist" aria-label="Assistant personas">
            <?php foreach ($tabs as $i => $tab): ?>
                <?php if (!is_array($tab)) continue; ?>
                <button
                    type="button"
                    role="tab"
                    class="xr-assist__tab<?= $i === 0 ? ' is-active' : '' ?>"
                    data-assist-tab
                    data-index="<?= (int)$i ?>"
                    aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"
                ><?= h((string)($tab['label'] ?? '')) ?></button>
            <?php endforeach; ?>
        </div>

        <?php foreach ($tabs as $i => $tab): ?>
            <?php if (!is_array($tab)) continue; ?>
            <?php $items = is_array($tab['items'] ?? null) ? $tab['items'] : []; ?>
            <div class="xr-assist__panel<?= $i === 0 ? ' is-active' : '' ?>" data-assist-panel data-index="<?= (int)$i ?>" role="tabpanel">
                <ul class="xr-assist__list" role="list">
                    <?php foreach ($items as $it): ?>
                        <?php $t = trim((string)$it); if ($t === '') continue; ?>
                        <li><?= h($t) ?></li>
                    <?php endforeach; ?>
                </ul>
                <p class="xr-assist__footer">
                    <span class="xr-assist__footer-a"><?= h($footerPrefix) ?></span>
                    <span class="xr-assist__footer-b"><?= h((string)($tab['footer'] ?? '')) ?></span>
                </p>
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

function xr_block_nextgen_image_slider(array $p, string $blockId = ''): void
{
    $title = (string)($p['title'] ?? '');
    $tabs = is_array($p['tabs'] ?? null) ? $p['tabs'] : [];
    ?>
    <div class="xr-nextgen" data-nextgen>
        <div class="xr-nextgen__chips" aria-hidden="true">
            <span class="xr-nextgen__chip"><?= h((string)($p['chip_left'] ?? 'XR Doctor')) ?></span>
            <span class="xr-nextgen__chip"><?= h((string)($p['chip_right'] ?? 'Exclusive')) ?></span>
        </div>
        <?php if ($title !== ''): ?>
            <h2 class="xr-nextgen__title"><?= h($title) ?></h2>
        <?php endif; ?>
        <div class="xr-nextgen__tabbar" role="tablist">
            <?php foreach ($tabs as $i => $tab): ?>
                <?php if (!is_array($tab)) continue; ?>
                <button type="button" class="xr-nextgen__tab<?= $i === 0 ? ' is-active' : '' ?>" data-nextgen-tab data-index="<?= (int)$i ?>" role="tab">
                    <?= h((string)($tab['label'] ?? '')) ?>
                </button>
            <?php endforeach; ?>
        </div>
        <?php foreach ($tabs as $i => $tab): ?>
            <?php if (!is_array($tab)) continue; ?>
            <div class="xr-nextgen__panel<?= $i === 0 ? ' is-active' : '' ?>" data-nextgen-panel data-index="<?= (int)$i ?>" role="tabpanel">
                <p class="xr-nextgen__subtitle"><?= h((string)($tab['subtitle'] ?? '')) ?></p>
                <?php if ((string)($tab['image'] ?? '') !== ''): ?>
                    <div class="xr-nextgen__image-wrap">
                        <img class="xr-nextgen__image" src="<?= h((string)($tab['image'] ?? '')) ?>" alt="">
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
}

function xr_block_challenges_glasses(array $p, string $blockId = ''): void
{
    $title = (string)($p['title'] ?? '');
    $deviceImage = (string)($p['device_image'] ?? '');
    $deviceCaption = (string)($p['device_caption'] ?? '');
    $deviceCaptionImage = (string)($p['device_caption_image'] ?? '');
    $cols = is_array($p['columns'] ?? null) ? $p['columns'] : [];
    ?>
    <div class="xr-challenges">
        <div class="xr-challenges__hero">
            <?php if ($title !== ''): ?>
                <h2 class="xr-challenges__title"><?= h($title) ?></h2>
            <?php endif; ?>
            <div class="xr-challenges__device">
                <?php if ($deviceImage !== ''): ?>
                    <img class="xr-challenges__device-img" src="<?= h($deviceImage) ?>" alt="">
                <?php endif; ?>
                <?php if ($deviceCaptionImage !== ''): ?>
                    <img class="xr-challenges__device-cap-img" src="<?= h($deviceCaptionImage) ?>" alt="">
                <?php elseif ($deviceCaption !== ''): ?>
                    <p class="xr-challenges__device-cap"><?= h($deviceCaption) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="xr-challenges__grid">
            <?php foreach ($cols as $col): ?>
                <?php if (!is_array($col)) continue; ?>
                <article class="xr-challenges__col">
                    <p class="xr-challenges__col-head"><?= h((string)($col['head'] ?? '')) ?></p>
                    <?php
                    $groups = is_array($col['groups'] ?? null) ? $col['groups'] : [];
                    foreach ($groups as $g):
                        if (!is_array($g)) continue;
                    ?>
                        <div class="xr-challenges__group">
                            <h3 class="xr-challenges__group-title"><?= h((string)($g['title'] ?? '')) ?></h3>
                            <?php $items = is_array($g['items'] ?? null) ? $g['items'] : []; ?>
                            <?php if ($items !== []): ?>
                                <ul class="xr-challenges__list" role="list">
                                    <?php foreach ($items as $it): ?>
                                        <?php $txt = trim((string)$it); if ($txt === '') continue; ?>
                                        <li><?= h($txt) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

function xr_block_tele_mentoring_bullets(array $p, string $blockId = ''): void
{
    $title = (string)($p['title'] ?? '');
    $items = is_array($p['items'] ?? null) ? $p['items'] : [];
    $footerA = (string)($p['footer_a'] ?? '');
    $footerB = (string)($p['footer_b'] ?? '');
    ?>
    <div class="xr-mentor">
        <?php if ($title !== ''): ?>
            <h2 class="xr-mentor__title"><?= h($title) ?></h2>
        <?php endif; ?>
        <?php if ($items !== []): ?>
            <ul class="xr-mentor__list" role="list">
                <?php foreach ($items as $it): ?>
                    <?php $txt = trim((string)$it); if ($txt === '') continue; ?>
                    <li><?= h($txt) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <?php if ($footerA !== '' || $footerB !== ''): ?>
            <p class="xr-mentor__footer">
                <span class="xr-mentor__footer-a"><?= h($footerA) ?></span>
                <span class="xr-mentor__footer-b"><?= h($footerB) ?></span>
            </p>
        <?php endif; ?>
    </div>
    <?php
}

function xr_block_deploy_banner(array $p, string $blockId = ''): void
{
    $title = (string)($p['title'] ?? '');
    $subtitle = (string)($p['subtitle'] ?? '');
    ?>
    <div class="xr-deploy">
        <div class="xr-deploy__noise" aria-hidden="true"></div>
        <div class="xr-deploy__inner">
            <?php if ($title !== ''): ?>
                <h2 class="xr-deploy__title"><?= h($title) ?></h2>
            <?php endif; ?>
            <?php if ($subtitle !== ''): ?>
                <p class="xr-deploy__subtitle"><?= h($subtitle) ?></p>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

function xr_block_text_heading_anim(array $p, string $blockId = ''): void
{
    // Backward-compatible renderer: old text_heading_anim now shows deploy banner design.
    $title = trim((string)($p['title'] ?? ''));
    if ($title === '') {
        $title = trim((string)($p['headline'] ?? ''));
    }
    if ($title === 'Designed for accountable outcomes') {
        $title = "Deploy XR Doctor\nfor Next - LeveL Mastery";
    }
    if ($title === '') {
        $title = "Deploy XR Doctor\nfor Next - LeveL Mastery";
    }

    $subtitle = trim((string)($p['subtitle'] ?? ''));
    if ($subtitle === '') {
        $subtitle = trim((string)($p['paragraph'] ?? ''));
    }
    if ($subtitle === 'Animated headline entrance + supporting copy for scanning.') {
        $subtitle = 'See how 6 simple steps take you from a CT scan to Next-GeN Reality';
    }
    if ($subtitle === '') {
        $subtitle = "See how 6 simple steps take you from a CT scan to Next-GeN Reality";
    }

    xr_block_deploy_banner([
        'title' => $title,
        'subtitle' => $subtitle,
    ], $blockId);
}

function xr_block_tabs_top_images(array $p, string $blockId = ''): void
{
    $title = trim((string)($p['title'] ?? ''));
    if ($title === '') {
        $title = 'Your Workflow with XR Doctor';
    }
    $subtitle = trim((string)($p['subtitle'] ?? ''));
    if ($subtitle === '') {
        $subtitle = 'Easy Start. Simple Steps. Next-GeN Reality.';
    }
    $footer = trim((string)($p['footer'] ?? ''));
    if ($footer === '') {
        $footer = 'Built for real professionals. Ready to be a part of your daily work.';
    }

    $legacyImgs = is_array($p['images'] ?? null) ? $p['images'] : [];
    $legacyTabs = is_array($p['tabs'] ?? null) ? $p['tabs'] : [];
    $rawSteps = is_array($p['steps'] ?? null) ? $p['steps'] : [];
    $steps = [];
    for ($i = 0; $i < 6; $i++) {
        $row = is_array($rawSteps[$i] ?? null) ? $rawSteps[$i] : [];
        $legacy = is_array($legacyTabs[$i] ?? null) ? $legacyTabs[$i] : [];
        $num = ['I.', 'II.', 'III.', 'IV.', 'V.', 'VI.'][$i] ?? (($i + 1) . '.');
        $steps[] = [
            'title' => trim((string)($row['title'] ?? ($legacy['label'] ?? ($num . ' Step')))),
            'text' => trim((string)($row['text'] ?? ($legacy['body'] ?? ''))),
            'image' => trim((string)($row['image'] ?? ($legacyImgs[$i] ?? ''))),
        ];
    }
    ?>
    <div class="xr-workflow">
        <h2 class="xr-workflow__title"><?= h($title) ?></h2>
        <p class="xr-workflow__subtitle"><?= h($subtitle) ?></p>

        <div class="xr-workflow__steps">
            <?php foreach ($steps as $s): ?>
                <article class="xr-workflow__step">
                    <div class="xr-workflow__media">
                        <?php if ($s['image'] !== ''): ?>
                            <img src="<?= h($s['image']) ?>" alt="">
                        <?php endif; ?>
                    </div>
                    <p class="xr-workflow__step-title"><?= h($s['title']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="xr-workflow__line" aria-hidden="true"></div>
        <div class="xr-workflow__dots" aria-hidden="true">
            <?php foreach ($steps as $_): ?><span class="xr-workflow__dot"></span><?php endforeach; ?>
        </div>

        <div class="xr-workflow__notes">
            <?php foreach ($steps as $s): ?>
                <p><?= h($s['text']) ?></p>
            <?php endforeach; ?>
        </div>

        <p class="xr-workflow__footer"><?= h($footer) ?></p>
    </div>
    <?php
}

function xr_block_pricing_creative(array $p, string $blockId = ''): void
{
    $eyebrow = trim((string)($p['eyebrow'] ?? ''));
    if ($eyebrow === '') {
        $eyebrow = 'XR Doctor knows what matters & designed plans that work for you';
    }
    $title = trim((string)($p['title'] ?? ''));
    if ($title === '') {
        $title = "Choose Your Plan\n& Let XR Doctor\nEmpower Your Every Day";
    }
    ?>
    <div class="xr-plan-banner">
        <div class="xr-plan-banner__noise" aria-hidden="true"></div>
        <div class="xr-plan-banner__inner">
            <p class="xr-plan-banner__eyebrow"><?= h($eyebrow) ?></p>
            <h2 class="xr-plan-banner__title"><?= nl2br(h($title)) ?></h2>
        </div>
    </div>
    <?php
}

function xr_block_coming_soon_anim(array $p, string $blockId = ''): void
{
    $title = trim((string)($p['title'] ?? ''));
    if ($title === '') {
        $title = "Affordable\nPrices for\nEveryone";
    }
    $tabs = is_array($p['tabs'] ?? null) ? $p['tabs'] : [];
    if ($tabs === []) {
        $tabs = [
            ['label' => 'Glasses Licence'],
            ['label' => 'Content Cost'],
        ];
    }
    $panels = is_array($p['panels'] ?? null) ? $p['panels'] : [];
    if ($panels === []) {
        $panels = [
            [
                'cards' => [
                    [
                        'badge' => 'VR Glasses',
                        'price' => '$65 mo/$699 year',
                        'items' => [
                            'Inside a fully virtual space',
                            'Work with interactive hologram',
                            'Explore clinical and simulated cases',
                            'Precise diagnosis and preparation',
                        ],
                        'cta' => 'Pre-Order',
                    ],
                    [
                        'badge' => 'AR Glasses',
                        'price' => '$95 mo/$999 year',
                        'items' => [
                            'Matched with the physical world',
                            'Work with interactive mapped hologram',
                            'Handle standard and complex cases',
                            'Enhance diagnostics via real overlay',
                        ],
                        'cta' => 'Pre-Order',
                    ],
                ],
            ],
            [
                'cards' => [
                    [
                        'badge' => 'FREE',
                        'price' => '',
                        'items' => [
                            'Use your own 3D models',
                            'Upload as many as you need',
                            'Built to fit the platform',
                            'Included in every plan',
                            'No extra cost - start now',
                        ],
                    ],
                    [
                        'badge' => 'From $7',
                        'price' => '',
                        'items' => [
                            'Standard CT-scans - $10',
                            'Prepaid bundles: 10 CT-scans - $90',
                            '25 CT-scans - $200',
                            '50 CT-scans - $350',
                            'Usage tracked in your account',
                        ],
                    ],
                ],
            ],
        ];
    }
    ?>
    <div class="xr-afford" data-afford>
        <div class="xr-afford__left">
            <h2 class="xr-afford__title"><?= nl2br(h($title)) ?></h2>
            <div class="xr-afford__tabs" role="tablist">
                <?php foreach ($tabs as $i => $tab): ?>
                    <?php $lbl = trim((string)($tab['label'] ?? '')); if ($lbl === '') continue; ?>
                    <button type="button" class="xr-afford__tab<?= $i === 0 ? ' is-active' : '' ?>" data-afford-tab data-index="<?= (int)$i ?>" aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"><?= h($lbl) ?></button>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="xr-afford__right">
            <?php foreach ($panels as $pi => $panel): ?>
                <?php
                if (!is_array($panel)) {
                    continue;
                }
                $cards = is_array($panel['cards'] ?? null) ? $panel['cards'] : [];
                ?>
                <div class="xr-afford__panel<?= $pi === 0 ? ' is-active' : '' ?>" data-afford-panel data-index="<?= (int)$pi ?>">
                    <?php foreach ($cards as $card): ?>
                        <?php if (!is_array($card)) continue; ?>
                        <article class="xr-afford__card">
                            <div class="xr-afford__badge"><?= h((string)($card['badge'] ?? '')) ?></div>
                            <?php if (((string)($card['price'] ?? '')) !== ''): ?>
                                <p class="xr-afford__price"><?= h((string)($card['price'] ?? '')) ?></p>
                            <?php endif; ?>
                            <?php $items = is_array($card['items'] ?? null) ? $card['items'] : []; ?>
                            <?php if ($items !== []): ?>
                                <ul class="xr-afford__list" role="list">
                                    <?php foreach ($items as $it): ?>
                                        <?php $txt = trim((string)$it); if ($txt === '') continue; ?>
                                        <li><?= h($txt) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                            <?php if (((string)($card['cta'] ?? '')) !== ''): ?>
                                <button type="button" class="xr-afford__cta"><?= h((string)($card['cta'] ?? '')) ?></button>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

function xr_block_stagger_lines(array $p, string $blockId = ''): void
{
    $columns = is_array($p['columns'] ?? null) ? $p['columns'] : [];
    if ($columns === []) {
        $columns = [
            [
                'title' => 'World-Class Product',
                'text' => 'XR Doctor - Next-GeN interactive holographic tool with AI support to leap your growth & boost success setting the new world standard',
            ],
            [
                'title' => 'World-Wide Network',
                'text' => 'Build a global network & become part of XR Doctor community for sharing expertise, hologram-guided telementoring via instant access to XR/AI-powered shared space',
            ],
            [
                'title' => 'World-Level Mastery',
                'text' => 'Achieve professional excellence & bring your skills to global standards with live layered mapped holograms & enhanced AI Clinical Assistance',
            ],
        ];
    }
    ?>
    <div class="xr-stagger">
        <div class="xr-stagger__cols">
            <?php foreach ($columns as $col): ?>
                <?php if (!is_array($col)) continue; ?>
                <article class="xr-stagger__col">
                    <h3 class="xr-stagger__title"><?= h((string)($col['title'] ?? '')) ?></h3>
                    <p class="xr-stagger__text"><?= h((string)($col['text'] ?? '')) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
        <div class="xr-stagger__arc" aria-hidden="true">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    <?php
}

function xr_block_image_pulse_cta(array $p, string $blockId = ''): void
{
    $badge = trim((string)($p['badge'] ?? ''));
    if ($badge === '') {
        $badge = 'Stay tuned.';
    }
    $title = trim((string)($p['title'] ?? ''));
    if ($title === '') {
        $title = 'XR Doctor. Already Here.';
    }
    ?>
    <div class="xr-ready">
        <div class="xr-ready__inner">
            <p class="xr-ready__badge"><?= h($badge) ?></p>
            <h2 class="xr-ready__title"><?= h($title) ?></h2>
        </div>
        <div class="xr-ready__arc" aria-hidden="true">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    <?php
}

function xr_block_reveal_outro(array $p, string $blockId = ''): void
{
    $image = trim((string)($p['image'] ?? ''));
    $text = trim((string)($p['text'] ?? ''));
    if ($text === '') {
        $oldTitle = trim((string)($p['title'] ?? ''));
        $oldBody = trim((string)($p['body'] ?? ''));
        $text = trim($oldTitle . ' ' . $oldBody);
    }
    if ($text === '') {
        $text = 'Take the World into Your Hands. Unlock XR Doctor Power for Your Mastery & Edge.';
    }
    ?>
    <div class="xr-outro">
        <div class="xr-outro__media">
            <?php if ($image !== ''): ?>
                <img src="<?= h($image) ?>" alt="">
            <?php endif; ?>
        </div>
        <div class="xr-outro__copy">
            <p><?= nl2br(h($text)) ?></p>
        </div>
    </div>
    <?php
}

function xr_block_preorder_banner(array $p, string $blockId = ''): void
{
    $title = trim((string)($p['title'] ?? ''));
    if ($title === '') {
        $title = "Leap into Your Next-GeN\nMedical Excellence";
    }
    $subtitle = trim((string)($p['subtitle'] ?? ''));
    if ($subtitle === '') {
        $subtitle = 'Pre-Order Now - Reserve Your Access';
    }
    $note = trim((string)($p['note'] ?? ''));
    if ($note === '') {
        $note = 'Get 3 Years Bonus - 1 Free Month Each Year';
    }
    $button = trim((string)($p['button_label'] ?? ''));
    if ($button === '') {
        $button = 'Pre-Order Now';
    }
    $href = trim((string)($p['href'] ?? '#'));
    ?>
    <div class="xr-preorder">
        <h2 class="xr-preorder__title"><?= nl2br(h($title)) ?></h2>
        <p class="xr-preorder__subtitle"><?= h($subtitle) ?></p>
        <p class="xr-preorder__note"><?= h($note) ?></p>
        <a class="xr-preorder__btn" href="<?= h($href) ?>"><?= h($button) ?></a>
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
