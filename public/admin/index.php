<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/includes/bootstrap.php';
require_once dirname(__DIR__, 2) . '/includes/block_helpers.php';

require_admin();

$site = load_site();
$flash = $_SESSION['admin_flash'] ?? '';
unset($_SESSION['admin_flash']);
$flashText = admin_render_flash($flash);

$navItems = $site['nav']['items'];
$hero = xr_find_block_props($site['home']['blocks'] ?? [], 'hero_fullscreen');
$intro = xr_find_block_props($site['home']['blocks'] ?? [], 'intro_gradient');
$assistant = xr_product_tabs_state($site);
$oculusTabs = xr_oculus_tabs_home_state($site);
$closing  = xr_find_block_props($site['home']['blocks'] ?? [], 'closing_block');
$vfreeze  = xr_find_block_props($site['home']['blocks'] ?? [], 'video_freeze_section');
$clinical   = xr_clinical_circles_state($site);
$visioners  = xr_team_visioners_state($site);
$galleryThree = xr_gallery_three_state($site);
$savesYour    = xr_saves_your_state($site);
$profState    = xr_professionals_state($site);
$instState    = xr_institutions_state($site);
$blogState    = xr_blog_state($site);
$parState     = xr_partners_state($site);
$hub = $site['hubspot'] ?? [];
$seo = is_array($site['seo'] ?? null) ? $site['seo'] : xr_site_seo_defaults();

$uiLang = admin_lang();
$token = csrf_token();
?>
<!DOCTYPE html>
<html lang="<?= h($uiLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h(admin_t('page.main_title')) ?></title>
    <link rel="stylesheet" href="/admin/admin.css">
</head>
<body class="admin-body admin-body--full">
<header class="admin-top admin-top--split">
    <div class="admin-top__lead">
        <strong class="admin-top__title"><?= h(admin_t('nav.brand')) ?></strong>
        <div class="admin-lang" role="group" aria-label="<?= h(admin_t('lang.label')) ?>">
            <a class="admin-lang__link<?= $uiLang === 'ru' ? ' is-active' : '' ?>" href="<?= h(admin_lang_switch_url('ru')) ?>"><?= h(admin_t('lang.ru')) ?></a>
            <span class="admin-lang__sep" aria-hidden="true">·</span>
            <a class="admin-lang__link<?= $uiLang === 'en' ? ' is-active' : '' ?>" href="<?= h(admin_lang_switch_url('en')) ?>"><?= h(admin_t('lang.en')) ?></a>
        </div>
    </div>
    <span class="admin-top__links">
        <a href="/" target="_blank" rel="noopener"><?= h(admin_t('nav.preview_home')) ?></a>
        <a href="/institutions.php" target="_blank" rel="noopener"><?= h(admin_t('nav.preview_institutions')) ?></a>
        <a href="/professionals.php" target="_blank" rel="noopener"><?= h(admin_t('nav.preview_professionals')) ?></a>
        <a href="/blog.php" target="_blank" rel="noopener"><?= h(admin_t('nav.preview_blog')) ?></a>
        <a href="/partners.php" target="_blank" rel="noopener"><?= h(admin_t('nav.preview_partners')) ?></a>
        <a href="/admin/media.php"><?= h(admin_t('nav.media')) ?></a>
        <a href="/admin/logout.php"><?= h(admin_t('nav.logout')) ?></a>
    </span>
</header>

<nav class="admin-toc" aria-label="<?= h(admin_t('toc.label')) ?>">
    <a class="admin-toc__link" href="#section-seo-global"><?= h(admin_t('toc.seo_global')) ?></a>
    <a class="admin-toc__link" href="#section-seo-pages"><?= h(admin_t('toc.seo_pages')) ?></a>
    <a class="admin-toc__link" href="#section-hubspot"><?= h(admin_t('toc.hubspot')) ?></a>
    <a class="admin-toc__link" href="#section-header"><?= h(admin_t('toc.header')) ?></a>
    <a class="admin-toc__link" href="#section-home">Home</a>
    <a class="admin-toc__link" href="#section-professionals">Professionals</a>
    <a class="admin-toc__link" href="#section-institutions">Institutions</a>
    <a class="admin-toc__link" href="#section-blog">Blog</a>
    <a class="admin-toc__link" href="#section-partners">Partners</a>
    <a class="admin-toc__link" href="#section-json"><?= h(admin_t('toc.json')) ?></a>
</nav>

<?php if ($flashText !== ''): ?>
    <p class="admin-flash"><?= h($flashText) ?></p>
<?php endif; ?>

<form class="admin-form" method="post" action="/admin/save.php">
    <input type="hidden" name="csrf" value="<?= h($token) ?>">

    <section id="section-seo-global" class="admin-section">
        <details class="admin-disclosure" open>
            <summary class="admin-disclosure__summary"><?= h(admin_t('section.seo_global')) ?></summary>
            <div class="admin-disclosure__body">
                <p class="admin-hint"><?= h(admin_t('hint.seo_global')) ?></p>
                <div class="admin-grid2">
                    <div>
                        <label class="admin-label"><?= h(admin_t('seo.site_url')) ?></label>
                        <input class="admin-input" name="seo[canonical_origin]" value="<?= h((string) ($seo['canonical_origin'] ?? '')) ?>" placeholder="https://www.example.com">
                        <label class="admin-label"><?= h(admin_t('seo.site_name')) ?></label>
                        <input class="admin-input" name="seo[site_name]" value="<?= h((string) ($seo['site_name'] ?? '')) ?>">
                        <label class="admin-label"><?= h(admin_t('seo.append_title')) ?></label>
                        <input type="hidden" name="seo[append_site_name]" value="0">
                        <label class="admin-check"><input type="checkbox" name="seo[append_site_name]" value="1" <?= !empty($seo['append_site_name']) ? 'checked' : '' ?>> <span><?= h(admin_t('seo.append_title_help')) ?></span></label>
                    </div>
                    <div>
                        <label class="admin-label"><?= h(admin_t('seo.title_sep')) ?></label>
                        <input class="admin-input" name="seo[title_separator]" value="<?= h((string) ($seo['title_separator'] ?? ' · ')) ?>" placeholder=" · ">
                        <label class="admin-label"><?= h(admin_t('seo.html_lang')) ?></label>
                        <input class="admin-input" name="seo[language]" value="<?= h((string) ($seo['language'] ?? 'en')) ?>" placeholder="en">
                        <label class="admin-label"><?= h(admin_t('seo.og_locale')) ?></label>
                        <input class="admin-input" name="seo[locale]" value="<?= h((string) ($seo['locale'] ?? 'en_US')) ?>" placeholder="en_US">
                    </div>
                </div>
                <label class="admin-label"><?= h(admin_t('seo.default_og_image')) ?></label>
                <input class="admin-input" name="seo[default_og_image]" value="<?= h((string) ($seo['default_og_image'] ?? '')) ?>" placeholder="/assets/img/logo.png">
                <div class="admin-grid2">
                    <div>
                        <label class="admin-label"><?= h(admin_t('seo.twitter_site')) ?></label>
                        <input class="admin-input" name="seo[twitter_site]" value="<?= h((string) ($seo['twitter_site'] ?? '')) ?>">
                    </div>
                    <div>
                        <label class="admin-label"><?= h(admin_t('seo.twitter_creator')) ?></label>
                        <input class="admin-input" name="seo[twitter_creator]" value="<?= h((string) ($seo['twitter_creator'] ?? '')) ?>">
                    </div>
                </div>
                <label class="admin-label"><?= h(admin_t('seo.fb_app_id')) ?></label>
                <input class="admin-input" name="seo[facebook_app_id]" value="<?= h((string) ($seo['facebook_app_id'] ?? '')) ?>">
                <p class="admin-hint"><?= h(admin_t('seo.org_block')) ?></p>
                <div class="admin-grid2">
                    <div>
                        <label class="admin-label"><?= h(admin_t('seo.org_name')) ?></label>
                        <input class="admin-input" name="seo[organization_name]" value="<?= h((string) ($seo['organization_name'] ?? '')) ?>">
                        <label class="admin-label"><?= h(admin_t('seo.org_url')) ?></label>
                        <input class="admin-input" name="seo[organization_url]" value="<?= h((string) ($seo['organization_url'] ?? '')) ?>" placeholder="/">
                    </div>
                    <div>
                        <label class="admin-label"><?= h(admin_t('seo.org_logo')) ?></label>
                        <input class="admin-input" name="seo[organization_logo_url]" value="<?= h((string) ($seo['organization_logo_url'] ?? '')) ?>">
                    </div>
                </div>
            </div>
        </details>
    </section>

    <section id="section-seo-pages" class="admin-section">
        <h2 class="visually-hidden"><?= h(admin_t('section.seo_pages')) ?></h2>
        <?php foreach (XR_PAGE_SLUGS as $slug): ?>
            <?php
            $m = is_array($site[$slug]['meta'] ?? null) ? $site[$slug]['meta'] : xr_page_meta_schema();
            $openFirst = $slug === 'home';
            ?>
            <details class="admin-disclosure admin-disclosure--nested"<?= $openFirst ? ' open' : '' ?>>
                <summary class="admin-disclosure__summary"><?= h(admin_t('section.seo_pages')) ?> — <?= h(admin_page_label($slug)) ?></summary>
                <div class="admin-disclosure__body">
                    <label class="admin-label"><?= h(admin_t('meta.doc_title')) ?></label>
                    <input class="admin-input" name="page_meta[<?= h($slug) ?>][title]" value="<?= h((string) ($m['title'] ?? '')) ?>">
                    <label class="admin-label"><?= h(admin_t('meta.description')) ?></label>
                    <textarea class="admin-textarea admin-textarea--sm" name="page_meta[<?= h($slug) ?>][description]" rows="2"><?= h((string) ($m['description'] ?? '')) ?></textarea>
                    <label class="admin-label"><?= h(admin_t('meta.keywords')) ?></label>
                    <input class="admin-input" name="page_meta[<?= h($slug) ?>][keywords]" value="<?= h((string) ($m['keywords'] ?? '')) ?>">
                    <label class="admin-label"><?= h(admin_t('meta.robots')) ?></label>
                    <input class="admin-input" name="page_meta[<?= h($slug) ?>][robots]" value="<?= h((string) ($m['robots'] ?? '')) ?>" placeholder="<?= h(admin_t('meta.robots_ph')) ?>">
                    <label class="admin-label"><?= h(admin_t('meta.canonical')) ?></label>
                    <input class="admin-input" name="page_meta[<?= h($slug) ?>][canonical_path]" value="<?= h((string) ($m['canonical_path'] ?? '')) ?>" placeholder="<?= h(admin_t('meta.canonical_ph')) ?> <?= h(xr_seo_page_path($slug)) ?>">
                    <p class="admin-hint"><?= h(admin_t('meta.og_block')) ?></p>
                    <div class="admin-grid2">
                        <div>
                            <label class="admin-label"><?= h(admin_t('meta.og_title')) ?></label>
                            <input class="admin-input" name="page_meta[<?= h($slug) ?>][og_title]" value="<?= h((string) ($m['og_title'] ?? '')) ?>">
                            <label class="admin-label"><?= h(admin_t('meta.og_image')) ?></label>
                            <input class="admin-input" name="page_meta[<?= h($slug) ?>][og_image]" value="<?= h((string) ($m['og_image'] ?? '')) ?>">
                            <label class="admin-label"><?= h(admin_t('meta.og_type')) ?></label>
                            <select class="admin-input" name="page_meta[<?= h($slug) ?>][og_type]">
                                <?php $ot = (string) ($m['og_type'] ?? 'website'); ?>
                                <option value="website" <?= $ot === 'website' ? 'selected' : '' ?>>website</option>
                                <option value="article" <?= $ot === 'article' ? 'selected' : '' ?>>article</option>
                            </select>
                        </div>
                        <div>
                            <label class="admin-label"><?= h(admin_t('meta.og_desc')) ?></label>
                            <textarea class="admin-textarea admin-textarea--sm" name="page_meta[<?= h($slug) ?>][og_description]" rows="3"><?= h((string) ($m['og_description'] ?? '')) ?></textarea>
                            <label class="admin-label"><?= h(admin_t('meta.tw_card')) ?></label>
                            <select class="admin-input" name="page_meta[<?= h($slug) ?>][twitter_card]">
                                <?php $tc = (string) ($m['twitter_card'] ?? 'summary_large_image'); ?>
                                <option value="summary_large_image" <?= $tc === 'summary_large_image' ? 'selected' : '' ?>>summary_large_image</option>
                                <option value="summary" <?= $tc === 'summary' ? 'selected' : '' ?>>summary</option>
                                <option value="app" <?= $tc === 'app' ? 'selected' : '' ?>>app</option>
                                <option value="player" <?= $tc === 'player' ? 'selected' : '' ?>>player</option>
                            </select>
                        </div>
                    </div>
                </div>
            </details>
        <?php endforeach; ?>
    </section>

    <section id="section-hubspot" class="admin-section">
        <details class="admin-disclosure" open>
            <summary class="admin-disclosure__summary"><?= h(admin_t('section.hubspot')) ?></summary>
            <div class="admin-disclosure__body">
                <p class="admin-hint"><?= h(admin_t('hint.hubspot')) ?></p>
                <label class="admin-label"><?= h(admin_t('hubspot.whitepaper_url')) ?></label>
                <input class="admin-input" name="hubspot_whitepaper" value="<?= h((string) ($hub['whitepaper_url'] ?? '')) ?>" placeholder="https://...">
                <label class="admin-label"><?= h(admin_t('hubspot.demo_url')) ?></label>
                <input class="admin-input" name="hubspot_demo_url" value="<?= h((string) ($hub['demo_url'] ?? '')) ?>" placeholder="https://...">
            </div>
        </details>
    </section>

    <section id="section-header" class="admin-section">
        <details class="admin-disclosure" open>
            <summary class="admin-disclosure__summary"><?= h(admin_t('section.header')) ?></summary>
            <div class="admin-disclosure__body">
                <label class="admin-label"><?= h(admin_t('nav.logo_alt')) ?></label>
                <input class="admin-input" name="logo_alt" value="<?= h((string) ($site['nav']['logo_alt'] ?? '')) ?>">
                <p class="admin-hint"><?= h(admin_t('hint.nav_pages')) ?></p>
                <div class="admin-repeater">
                    <?php foreach ($navItems as $i => $item): ?>
                        <?php if (!is_array($item)) {
                            continue;
                        } ?>
                        <div class="admin-row admin-row--nav">
                            <input class="admin-input" name="nav_label[]" placeholder="<?= h(admin_t('nav.label_ph')) ?>" value="<?= h((string) ($item['label'] ?? '')) ?>">
                            <input class="admin-input" name="nav_href[]" placeholder="<?= h(admin_t('nav.href_ph')) ?>" value="<?= h((string) ($item['href'] ?? '')) ?>">
                        </div>
                    <?php endforeach; ?>
                    <?php for ($j = 0; $j < 2; $j++): ?>
                        <div class="admin-row admin-row--nav">
                            <input class="admin-input" name="nav_label[]" placeholder="<?= h(admin_t('nav.new_item')) ?>">
                            <input class="admin-input" name="nav_href[]" placeholder="#">
                        </div>
                    <?php endfor; ?>
                </div>
                <div class="admin-grid2">
                    <div>
                        <label class="admin-label"><?= h(admin_t('nav.cta_wp_label')) ?></label>
                        <input class="admin-input" name="cta_outline_label" value="<?= h((string) ($site['nav']['cta_outline']['label'] ?? '')) ?>">
                        <label class="admin-label"><?= h(admin_t('nav.cta_wp_href')) ?></label>
                        <input class="admin-input" name="cta_outline_href" value="<?= h((string) ($site['nav']['cta_outline']['href'] ?? '')) ?>">
                    </div>
                    <div>
                        <label class="admin-label"><?= h(admin_t('nav.cta_demo_label')) ?></label>
                        <input class="admin-input" name="cta_gradient_label" value="<?= h((string) ($site['nav']['cta_gradient']['label'] ?? '')) ?>">
                        <label class="admin-label"><?= h(admin_t('nav.cta_demo_href')) ?></label>
                        <input class="admin-input" name="cta_gradient_href" value="<?= h((string) ($site['nav']['cta_gradient']['href'] ?? '')) ?>">
                    </div>
                </div>
            </div>
        </details>
    </section>

    <section id="section-home" class="admin-section">
        <details class="admin-disclosure" open>
            <summary class="admin-disclosure__summary"><?= h(admin_t('section.home_blocks')) ?></summary>
            <div class="admin-disclosure__body">
                <fieldset class="admin-fieldset admin-fieldset--flat">
                    <legend><?= h(admin_t('hero.legend')) ?></legend>
                    <label class="admin-label"><?= h(admin_t('hero.youtube')) ?></label>
                    <input class="admin-input" name="hero_youtube_id" value="<?= h((string) ($hero['youtube_id'] ?? '')) ?>" placeholder="sPJhprfQEQw или https://youtu.be/...">
                    <label class="admin-label"><?= h(admin_t('hero.poster')) ?></label>
                    <div class="admin-img-wrap">
                        <div class="admin-img-row">
                            <input class="admin-input admin-img-url" name="hero_poster" value="<?= h((string) ($hero['poster'] ?? '')) ?>" placeholder="/assets/img/hero-bg.png">
                            <button type="button" class="admin-btn admin-img-upload-btn" title="<?= h(admin_t('btn.upload')) ?>">↑</button>
                            <span class="admin-img-spin" hidden>…</span>
                        </div>
                        <input type="file" class="admin-img-file" accept="image/*" hidden>
                        <img class="admin-img-preview<?= ($hero['poster'] ?? '') === '' ? '' : '' ?>" src="<?= h((string) ($hero['poster'] ?? '')) ?>" alt=""<?= ($hero['poster'] ?? '') === '' ? ' hidden' : '' ?>>
                    </div>
                    <label class="admin-label"><?= h(admin_t('hero.mp4')) ?></label>
                    <input class="admin-input" name="hero_video_mp4" value="<?= h((string) ($hero['video_mp4'] ?? '')) ?>">
                    <label class="admin-label"><?= h(admin_t('hero.webm')) ?></label>
                    <input class="admin-input" name="hero_video_webm" value="<?= h((string) ($hero['video_webm'] ?? '')) ?>">
                    <label class="admin-label"><?= h(admin_t('hero.overlay_note')) ?></label>
                    <input class="admin-input" name="hero_overlay_note" value="<?= h((string) ($hero['overlay_note'] ?? '')) ?>">
                    <label class="admin-label"><?= h(admin_t('hero.overlay_lines')) ?></label>
                    <textarea class="admin-textarea" name="hero_overlay_lines" rows="3"><?= h(implode("\n", (array) ($hero['overlay_lines'] ?? []))) ?></textarea>
                </fieldset>

                <fieldset class="admin-fieldset admin-fieldset--flat">
                    <legend><?= h(admin_t('intro.legend')) ?></legend>
                    <label class="admin-label"><?= h(admin_t('intro.eyebrow')) ?></label>
                    <input class="admin-input" name="intro_eyebrow" value="<?= h((string) ($intro['eyebrow'] ?? '')) ?>">
                    <label class="admin-label"><?= h(admin_t('intro.line1')) ?></label>
                    <input class="admin-input" name="intro_headline_line1" value="<?= h((string) ($intro['headline_line1'] ?? '')) ?>">
                    <label class="admin-label"><?= h(admin_t('intro.line2')) ?></label>
                    <input class="admin-input" name="intro_headline_line2" value="<?= h((string) ($intro['headline_line2'] ?? '')) ?>">
                    <label class="admin-label"><?= h(admin_t('intro.body')) ?></label>
                    <textarea class="admin-textarea" name="intro_body" rows="5"><?= h((string) ($intro['body'] ?? $intro['tagline'] ?? '')) ?></textarea>
                </fieldset>

                <fieldset class="admin-fieldset admin-fieldset--flat" id="section-oculus">
                    <legend><?= h(admin_t('oculus.legend')) ?></legend>
                    <p class="admin-hint"><?= h(admin_t('oculus.hint')) ?></p>
                    <?php foreach ($oculusTabs['tabs'] as $oi => $ot): ?>
                        <p class="admin-hint"><strong><?= h(admin_t('oculus.tab_n', ['n' => (string) ($oi + 1)])) ?></strong><?php if ($ot['label'] !== ''): ?>: <?= h($ot['label']) ?><?php endif; ?></p>
                        <label class="admin-label"><?= h(admin_t('oculus.poster')) ?></label>
                        <input class="admin-input" name="oculus_tab_poster_<?= (int) $oi ?>" value="<?= h($ot['poster']) ?>" placeholder="/assets/img/...">
                        <label class="admin-label"><?= h(admin_t('oculus.youtube_id')) ?></label>
                        <input class="admin-input" name="oculus_tab_youtube_<?= (int) $oi ?>" value="<?= h($ot['youtube_id']) ?>" placeholder="dQw4w9WgXcQ">
                    <?php endforeach; ?>
                </fieldset>

                <fieldset class="admin-fieldset admin-fieldset--flat">
                    <legend><?= h(admin_t('assistant.legend')) ?></legend>
                    <p class="admin-hint"><?= h(admin_t('hint.assistant')) ?></p>
                    <label class="admin-label"><?= h(admin_t('assistant.video_yt')) ?></label>
                    <input class="admin-input" name="assistant_video_youtube_id" value="<?= h($assistant['video_youtube_id']) ?>" placeholder="dQw4w9WgXcQ или https://youtu.be/...">
                    <label class="admin-label"><?= h(admin_t('assistant.video_poster')) ?></label>
                    <div class="admin-img-wrap">
                        <div class="admin-img-row">
                            <input class="admin-input admin-img-url" name="assistant_video_poster" value="<?= h($assistant['video_poster']) ?>" placeholder="/assets/img/...">
                            <button type="button" class="admin-btn admin-img-upload-btn" title="<?= h(admin_t('btn.upload')) ?>">↑</button>
                            <span class="admin-img-spin" hidden>…</span>
                        </div>
                        <input type="file" class="admin-img-file" accept="image/*" hidden>
                        <img class="admin-img-preview" src="<?= h($assistant['video_poster']) ?>" alt=""<?= $assistant['video_poster'] === '' ? ' hidden' : '' ?>>
                    </div>
                    <label class="admin-label"><?= h(admin_t('assistant.video_label')) ?></label>
                    <input class="admin-input" name="assistant_video_label" value="<?= h($assistant['video_label']) ?>" placeholder="See XR Doctor Platform in Action">
                    <div class="admin-grid2">
                        <div>
                            <label class="admin-label"><?= h(admin_t('assistant.card_img_0')) ?></label>
                            <div class="admin-img-wrap">
                                <div class="admin-img-row">
                                    <input class="admin-input admin-img-url" name="assistant_card_image_0" value="<?= h($assistant['card_image_0']) ?>" placeholder="/assets/img/...">
                                    <button type="button" class="admin-btn admin-img-upload-btn" title="<?= h(admin_t('btn.upload')) ?>">↑</button>
                                    <span class="admin-img-spin" hidden>…</span>
                                </div>
                                <input type="file" class="admin-img-file" accept="image/*" hidden>
                                <?php if ($assistant['card_image_0'] !== ''): ?>
                                    <img class="admin-img-preview" src="<?= h($assistant['card_image_0']) ?>" alt="">
                                <?php else: ?>
                                    <img class="admin-img-preview" src="" alt="" hidden>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div>
                            <label class="admin-label"><?= h(admin_t('assistant.card_img_1')) ?></label>
                            <div class="admin-img-wrap">
                                <div class="admin-img-row">
                                    <input class="admin-input admin-img-url" name="assistant_card_image_1" value="<?= h($assistant['card_image_1']) ?>" placeholder="/assets/img/...">
                                    <button type="button" class="admin-btn admin-img-upload-btn" title="<?= h(admin_t('btn.upload')) ?>">↑</button>
                                    <span class="admin-img-spin" hidden>…</span>
                                </div>
                                <input type="file" class="admin-img-file" accept="image/*" hidden>
                                <?php if ($assistant['card_image_1'] !== ''): ?>
                                    <img class="admin-img-preview" src="<?= h($assistant['card_image_1']) ?>" alt="">
                                <?php else: ?>
                                    <img class="admin-img-preview" src="" alt="" hidden>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <p class="admin-label" style="margin-top:12px;font-weight:600"><?= h(admin_t('assistant.grid_icons')) ?></p>
                    <div class="admin-grid2">
                        <?php for ($gi = 0; $gi < 4; $gi++): ?>
                        <div>
                            <label class="admin-label"><?= h(admin_t('assistant.grid_icon_' . $gi)) ?></label>
                            <div class="admin-img-wrap">
                                <div class="admin-img-row">
                                    <input class="admin-input admin-img-url" name="assistant_grid_icon_img_<?= $gi ?>" value="<?= h($assistant['grid_icon_imgs'][$gi] ?? '') ?>" placeholder="/assets/img/...">
                                    <button type="button" class="admin-btn admin-img-upload-btn" title="<?= h(admin_t('btn.upload')) ?>">↑</button>
                                    <span class="admin-img-spin" hidden>…</span>
                                </div>
                                <input type="file" class="admin-img-file" accept="image/*" hidden>
                                <img class="admin-img-preview" src="<?= h($assistant['grid_icon_imgs'][$gi] ?? '') ?>" alt=""<?= ($assistant['grid_icon_imgs'][$gi] ?? '') === '' ? ' hidden' : '' ?>>
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                    <label class="admin-label"><?= h(admin_t('assistant.tabs')) ?></label>
                    <textarea class="admin-textarea" name="assistant_tabs" rows="4"><?= h(implode("\n", $assistant['tabs'])) ?></textarea>
                    <label class="admin-label"><?= h(admin_t('assistant.active')) ?></label>
                    <input class="admin-input admin-input--narrow" type="number" min="0" name="assistant_active_tab" value="<?= (int) $assistant['active_tab'] ?>">
                    <label class="admin-label"><?= h(admin_t('assistant.title')) ?></label>
                    <input class="admin-input" name="assistant_title" value="<?= h($assistant['title']) ?>">
                    <label class="admin-label"><?= h(admin_t('assistant.lead')) ?></label>
                    <textarea class="admin-textarea" name="assistant_lead" rows="5"><?= h($assistant['lead']) ?></textarea>
                    <label class="admin-label"><?= h(admin_t('assistant.p2')) ?></label>
                    <textarea class="admin-textarea" name="assistant_paragraph2" rows="4"><?= h($assistant['paragraph2']) ?></textarea>
                    <label class="admin-label"><?= h(admin_t('assistant.p3')) ?></label>
                    <textarea class="admin-textarea" name="assistant_paragraph3" rows="2"><?= h($assistant['paragraph3']) ?></textarea>
                    <label class="admin-label"><?= h(admin_t('assistant.sidebar')) ?></label>
                    <textarea class="admin-textarea" name="assistant_sidebar_title" rows="3"><?= h($assistant['sidebar_title']) ?></textarea>
                    <p class="admin-hint"><?= h(admin_t('assistant.features_hint')) ?></p>
                    <?php foreach ($assistant['features'] as $f): ?>
                        <input class="admin-input admin-input--feature" name="feature_label[]" value="<?= h((string) ($f['label'] ?? '')) ?>">
                    <?php endforeach; ?>
                    <input class="admin-input admin-input--feature" name="feature_label[]" placeholder="<?= h(admin_t('assistant.feature_ph')) ?>">
                    <label class="admin-label"><?= h(admin_t('assistant.bottom_title')) ?></label>
                    <input class="admin-input" name="assistant_bottom_title" value="<?= h($assistant['bottom_title']) ?>">
                    <label class="admin-label"><?= h(admin_t('assistant.link_label')) ?></label>
                    <input class="admin-input" name="assistant_bottom_link_label" value="<?= h((string) ($assistant['bottom_link']['label'] ?? '')) ?>">
                    <label class="admin-label"><?= h(admin_t('assistant.link_href')) ?></label>
                    <input class="admin-input" name="assistant_bottom_link_href" value="<?= h((string) ($assistant['bottom_link']['href'] ?? '')) ?>">
                </fieldset>

                <fieldset class="admin-fieldset admin-fieldset--flat">
                    <legend><?= h(admin_t('vfreeze.legend')) ?></legend>
                    <p class="admin-hint"><?= h(admin_t('vfreeze.hint')) ?></p>
                    <label class="admin-label"><?= h(admin_t('vfreeze.youtube')) ?></label>
                    <input class="admin-input" name="vfreeze_youtube_id" value="<?= h((string) ($vfreeze['youtube_id'] ?? '')) ?>" placeholder="KFaxA3eWwXk или https://youtube.com/watch?v=...">
                    <label class="admin-label"><?= h(admin_t('vfreeze.mp4')) ?></label>
                    <input class="admin-input" name="vfreeze_mp4" value="<?= h((string) ($vfreeze['mp4'] ?? '')) ?>" placeholder="https://...video.mp4">
                    <label class="admin-label"><?= h(admin_t('vfreeze.poster')) ?></label>
                    <input class="admin-input" name="vfreeze_poster" value="<?= h((string) ($vfreeze['poster'] ?? '')) ?>" placeholder="/assets/img/...">
                    <div class="admin-grid2">
                        <div>
                            <label class="admin-label"><?= h(admin_t('vfreeze.heading')) ?></label>
                            <input class="admin-input" name="vfreeze_heading" value="<?= h((string) ($vfreeze['heading'] ?? '')) ?>">
                        </div>
                        <div>
                            <label class="admin-label"><?= h(admin_t('vfreeze.heading2')) ?></label>
                            <input class="admin-input" name="vfreeze_heading2" value="<?= h((string) ($vfreeze['heading_line2'] ?? '')) ?>">
                        </div>
                    </div>
                    <label class="admin-label"><?= h(admin_t('vfreeze.intro')) ?></label>
                    <textarea class="admin-textarea" name="vfreeze_intro" rows="3"><?= h((string) ($vfreeze['intro'] ?? '')) ?></textarea>
                    <label class="admin-label"><?= h(admin_t('vfreeze.caption')) ?></label>
                    <input class="admin-input" name="vfreeze_caption" value="<?= h((string) ($vfreeze['caption'] ?? '')) ?>">
                </fieldset>

                <fieldset class="admin-fieldset admin-fieldset--flat">
                    <legend>What You Need block (block-1-16-17)</legend>

                    <label class="admin-label">Изображение устройства (Apple Vision Pro и др.)</label>
                    <div class="admin-img-wrap">
                        <div class="admin-img-row">
                            <input class="admin-input admin-img-url" name="reqgrid_device_image" value="<?= h($galleryThree['device_image']) ?>" placeholder="/uploads/...">
                            <button type="button" class="admin-btn admin-img-upload-btn" title="<?= h(admin_t('btn.upload')) ?>">↑</button>
                            <span class="admin-img-spin" hidden>…</span>
                        </div>
                        <input type="file" class="admin-img-file" accept="image/*" hidden>
                        <img class="admin-img-preview" src="<?= h($galleryThree['device_image']) ?>" alt=""<?= $galleryThree['device_image'] === '' ? ' hidden' : '' ?>>
                    </div>

                    <p class="admin-hint" style="margin-top:16px"><strong>Иконки групп (можно загрузить изображение или вписать эмодзи)</strong></p>
                    <?php
                    $reqIconLabels = [
                        '0_0' => 'XR Doctor App → Choose Your App',
                        '0_1' => 'XR Doctor App → Buy Licence',
                        '1_0' => 'Content for App → XR Doctor Assistant',
                        '1_1' => 'Content for App → XR Doctor Training',
                    ];
                    foreach ($reqIconLabels as $key => $label):
                        $val = $galleryThree['icons'][$key] ?? '';
                        $isImg = str_starts_with($val, '/') || str_starts_with($val, 'http');
                    ?>
                        <label class="admin-label"><?= h($label) ?></label>
                        <div class="admin-img-wrap">
                            <div class="admin-img-row">
                                <input class="admin-input admin-img-url" name="reqgrid_icon_<?= h($key) ?>" value="<?= h($val) ?>" placeholder="эмодзи или /uploads/...">
                                <button type="button" class="admin-btn admin-img-upload-btn" title="<?= h(admin_t('btn.upload')) ?>">↑</button>
                                <span class="admin-img-spin" hidden>…</span>
                            </div>
                            <input type="file" class="admin-img-file" accept="image/*" hidden>
                            <?php if ($isImg): ?>
                                <img class="admin-img-preview" src="<?= h($val) ?>" alt="">
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>

                    <p class="admin-hint" style="margin-top:16px"><strong>AR/VR Glasses — иконки пунктов списка</strong></p>
                    <?php
                    $glassesLabels = [
                        'Choose suitable AR/VR glasses',
                        'AR for full functionality',
                        'VR for limited functionality',
                        'Buy your own for full price',
                        'Ask about installments',
                    ];
                    foreach ($glassesLabels as $ii => $glLabel):
                        $glVal = $galleryThree['item_icons'][$ii] ?? '';
                        $glIsImg = str_starts_with($glVal, '/') || str_starts_with($glVal, 'http');
                    ?>
                        <label class="admin-label"><?= h($glLabel) ?></label>
                        <div class="admin-img-wrap">
                            <div class="admin-img-row">
                                <input class="admin-input admin-img-url" name="reqgrid_item_icon_<?= (int) $ii ?>" value="<?= h($glVal) ?>" placeholder="эмодзи или /uploads/...">
                                <button type="button" class="admin-btn admin-img-upload-btn" title="<?= h(admin_t('btn.upload')) ?>">↑</button>
                                <span class="admin-img-spin" hidden>…</span>
                            </div>
                            <input type="file" class="admin-img-file" accept="image/*" hidden>
                            <?php if ($glIsImg): ?>
                                <img class="admin-img-preview" src="<?= h($glVal) ?>" alt="">
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </fieldset>

                <fieldset class="admin-fieldset admin-fieldset--flat">
                    <legend>XR Doctor Saves Your (block-1-18)</legend>
                    <?php
                    $savesLabels = ['Time', 'Effort', 'Money'];
                    foreach ($savesLabels as $si => $slabel):
                        $sVal = $savesYour['images'][$si] ?? '';
                    ?>
                        <label class="admin-label">Изображение — <?= h($slabel) ?></label>
                        <div class="admin-img-wrap">
                            <div class="admin-img-row">
                                <input class="admin-input admin-img-url" name="saves_img_<?= (int) $si ?>" value="<?= h($sVal) ?>" placeholder="/uploads/...">
                                <button type="button" class="admin-btn admin-img-upload-btn" title="<?= h(admin_t('btn.upload')) ?>">↑</button>
                                <span class="admin-img-spin" hidden>…</span>
                            </div>
                            <input type="file" class="admin-img-file" accept="image/*" hidden>
                            <img class="admin-img-preview" src="<?= h($sVal) ?>" alt=""<?= $sVal === '' ? ' hidden' : '' ?>>
                        </div>
                    <?php endforeach; ?>
                </fieldset>

                <fieldset class="admin-fieldset admin-fieldset--flat">
                    <legend><?= h(admin_t('visioners.legend')) ?></legend>
                    <div class="admin-grid2">
                        <?php for ($vi = 0; $vi < 2; $vi++): ?>
                        <div>
                            <label class="admin-label"><?= h(admin_t('visioners.photo_' . $vi)) ?></label>
                            <div class="admin-img-wrap">
                                <div class="admin-img-row">
                                    <input class="admin-input admin-img-url" name="visioners_photo_src_<?= $vi ?>" value="<?= h($visioners['photos'][$vi]['src']) ?>" placeholder="/assets/img/...">
                                    <button type="button" class="admin-btn admin-img-upload-btn" title="<?= h(admin_t('btn.upload')) ?>">↑</button>
                                    <span class="admin-img-spin" hidden>…</span>
                                </div>
                                <input type="file" class="admin-img-file" accept="image/*" hidden>
                                <img class="admin-img-preview" src="<?= h($visioners['photos'][$vi]['src']) ?>" alt=""<?= $visioners['photos'][$vi]['src'] === '' ? ' hidden' : '' ?>>
                            </div>
                            <label class="admin-label"><?= h(admin_t('visioners.photo_alt')) ?></label>
                            <input class="admin-input" name="visioners_photo_alt_<?= $vi ?>" value="<?= h($visioners['photos'][$vi]['alt']) ?>" placeholder="Alt text">
                        </div>
                        <?php endfor; ?>
                    </div>
                    <label class="admin-label"><?= h(admin_t('visioners.image')) ?></label>
                    <div class="admin-img-wrap">
                        <div class="admin-img-row">
                            <input class="admin-input admin-img-url" name="visioners_image" value="<?= h($visioners['image']) ?>" placeholder="/assets/img/...">
                            <button type="button" class="admin-btn admin-img-upload-btn" title="<?= h(admin_t('btn.upload')) ?>">↑</button>
                            <span class="admin-img-spin" hidden>…</span>
                        </div>
                        <input type="file" class="admin-img-file" accept="image/*" hidden>
                        <img class="admin-img-preview" src="<?= h($visioners['image']) ?>" alt=""<?= $visioners['image'] === '' ? ' hidden' : '' ?>>
                    </div>
                </fieldset>

                <fieldset class="admin-fieldset admin-fieldset--flat">
                    <legend><?= h(admin_t('clinical.legend')) ?></legend>
                    <div class="admin-grid2" style="grid-template-columns:repeat(3,1fr)">
                        <?php for ($ci = 0; $ci < 3; $ci++): ?>
                        <div>
                            <label class="admin-label"><?= h(admin_t('clinical.circle_' . $ci)) ?></label>
                            <div class="admin-img-wrap">
                                <div class="admin-img-row">
                                    <input class="admin-input admin-img-url" name="clinical_circle_src_<?= $ci ?>" value="<?= h($clinical['circles'][$ci]['src']) ?>" placeholder="/assets/img/...">
                                    <button type="button" class="admin-btn admin-img-upload-btn" title="<?= h(admin_t('btn.upload')) ?>">↑</button>
                                    <span class="admin-img-spin" hidden>…</span>
                                </div>
                                <input type="file" class="admin-img-file" accept="image/*" hidden>
                                <img class="admin-img-preview" src="<?= h($clinical['circles'][$ci]['src']) ?>" alt=""<?= $clinical['circles'][$ci]['src'] === '' ? ' hidden' : '' ?>>
                            </div>
                            <label class="admin-label"><?= h(admin_t('clinical.circle_alt')) ?></label>
                            <input class="admin-input" name="clinical_circle_alt_<?= $ci ?>" value="<?= h($clinical['circles'][$ci]['alt']) ?>" placeholder="Alt text">
                        </div>
                        <?php endfor; ?>
                    </div>
                </fieldset>

                <fieldset class="admin-fieldset admin-fieldset--flat">
                    <legend><?= h(admin_t('closing.legend')) ?></legend>
                    <label class="admin-label"><?= h(admin_t('closing.line1')) ?></label>
                    <input class="admin-input" name="closing_line1" value="<?= h((string) ($closing['line1'] ?? '')) ?>">
                    <label class="admin-label"><?= h(admin_t('closing.line2')) ?></label>
                    <input class="admin-input" name="closing_line2" value="<?= h((string) ($closing['line2'] ?? '')) ?>">
                </fieldset>
            </div>
        </details>
    </section>

    <!-- ═══════════════════ PROFESSIONALS ═══════════════════ -->
    <section id="section-professionals" class="admin-section">
        <details class="admin-disclosure">
            <summary class="admin-disclosure__summary">Professionals — блоки страницы</summary>
            <div class="admin-disclosure__body">

                <fieldset class="admin-fieldset admin-fieldset--flat">
                    <legend>Hero (p-2-intro)</legend>
                    <label class="admin-label">Заголовок</label>
                    <input class="admin-input" name="pro_hero_title" value="<?= h($profState['hero_title']) ?>">
                    <label class="admin-label">Подзаголовок</label>
                    <input class="admin-input" name="pro_hero_subtitle" value="<?= h($profState['hero_subtitle']) ?>">
                    <label class="admin-label">Изображение</label>
                    <div class="admin-img-wrap">
                        <div class="admin-img-row">
                            <input class="admin-input admin-img-url" name="pro_hero_image" value="<?= h($profState['hero_image']) ?>" placeholder="/assets/img/...">
                            <button type="button" class="admin-btn admin-img-upload-btn" title="Загрузить">↑</button>
                            <span class="admin-img-spin" hidden>…</span>
                        </div>
                        <input type="file" class="admin-img-file" accept="image/*" hidden>
                        <img class="admin-img-preview" src="<?= h($profState['hero_image']) ?>" alt=""<?= $profState['hero_image'] === '' ? ' hidden' : '' ?>>
                    </div>
                </fieldset>

                <fieldset class="admin-fieldset admin-fieldset--flat">
                    <legend>Engage Split (p-2-2)</legend>
                    <label class="admin-label">Кнопка-пилюля (eyebrow)</label>
                    <input class="admin-input" name="pro_engage_eyebrow" value="<?= h($profState['engage_eyebrow']) ?>">
                    <label class="admin-label">Заголовок (каждая строка с новой линии)</label>
                    <textarea class="admin-textarea admin-textarea--sm" name="pro_engage_title" rows="3"><?= h($profState['engage_title']) ?></textarea>
                    <label class="admin-label">Теглайны (каждый с новой строки)</label>
                    <textarea class="admin-textarea admin-textarea--sm" name="pro_engage_taglines" rows="3"><?= h($profState['engage_taglines']) ?></textarea>
                    <p class="admin-hint" style="margin-top:1rem">Карточка справа</p>
                    <label class="admin-label">Заголовок карточки</label>
                    <input class="admin-input" name="pro_engage_card_title" value="<?= h($profState['engage_card_title']) ?>">
                    <label class="admin-label">Иконка карточки</label>
                    <div class="admin-img-wrap">
                        <div class="admin-img-row">
                            <input class="admin-input admin-img-url" name="pro_engage_card_icon" value="<?= h($profState['engage_card_icon']) ?>" placeholder="/assets/img/...">
                            <button type="button" class="admin-btn admin-img-upload-btn" title="Загрузить">↑</button>
                            <span class="admin-img-spin" hidden>…</span>
                        </div>
                        <input type="file" class="admin-img-file" accept="image/*" hidden>
                        <img class="admin-img-preview" src="<?= h($profState['engage_card_icon']) ?>" alt=""<?= $profState['engage_card_icon'] === '' ? ' hidden' : '' ?>>
                    </div>
                    <label class="admin-label">YouTube ID или URL (видео в карточке)</label>
                    <input class="admin-input" name="pro_engage_card_yt" value="<?= h($profState['engage_card_yt']) ?>" placeholder="dQw4w9WgXcQ">
                    <label class="admin-label">Видео MP4 URL (если не YouTube)</label>
                    <div class="admin-img-wrap">
                        <div class="admin-img-row">
                            <input class="admin-input admin-img-url" name="pro_engage_card_mp4" value="<?= h($profState['engage_card_mp4']) ?>" placeholder="/uploads/...">
                            <button type="button" class="admin-btn admin-img-upload-btn" title="Загрузить">↑</button>
                            <span class="admin-img-spin" hidden>…</span>
                        </div>
                        <input type="file" class="admin-img-file" accept="video/mp4,video/webm" hidden>
                        <video class="admin-video-preview" src="<?= h($profState['engage_card_mp4']) ?>" controls muted playsinline<?= $profState['engage_card_mp4'] === '' ? ' hidden' : '' ?>></video>
                        <img class="admin-img-preview" src="" alt="" hidden>
                    </div>
                    <label class="admin-label">Постер видео</label>
                    <div class="admin-img-wrap">
                        <div class="admin-img-row">
                            <input class="admin-input admin-img-url" name="pro_engage_card_poster" value="<?= h($profState['engage_card_poster']) ?>" placeholder="/assets/img/...">
                            <button type="button" class="admin-btn admin-img-upload-btn" title="Загрузить">↑</button>
                            <span class="admin-img-spin" hidden>…</span>
                        </div>
                        <input type="file" class="admin-img-file" accept="image/*" hidden>
                        <img class="admin-img-preview" src="<?= h($profState['engage_card_poster']) ?>" alt=""<?= $profState['engage_card_poster'] === '' ? ' hidden' : '' ?>>
                    </div>
                </fieldset>

                <fieldset class="admin-fieldset admin-fieldset--flat">
                    <legend>YouTube Heading (p-2-5)</legend>
                    <label class="admin-label">Заголовок секции</label>
                    <input class="admin-input" name="pro_yt_heading" value="<?= h($profState['yt_heading']) ?>">
                    <label class="admin-label">YouTube ID или URL</label>
                    <input class="admin-input" name="pro_yt_id" value="<?= h($profState['yt_id']) ?>" placeholder="dQw4w9WgXcQ">
                </fieldset>

                <fieldset class="admin-fieldset admin-fieldset--flat">
                    <legend>Reality slider (p-2-3) — PNG для “искорки”</legend>
                    <label class="admin-label">Заголовок</label>
                    <input class="admin-input" name="pro_reality_title" value="<?= h($profState['reality_title'] ?? '') ?>">
                    <label class="admin-label">Интервал (мс)</label>
                    <input class="admin-input" name="pro_reality_interval_ms" value="<?= h($profState['reality_interval_ms'] ?? '') ?>" placeholder="3500">
                    <p class="admin-hint">Загрузите PNG (или вставьте URL) для каждого слайда.</p>
                    <div class="admin-grid2" style="grid-template-columns:repeat(2,1fr)">
                        <?php for ($ri = 0; $ri < 4; $ri++): ?>
                        <div>
                            <label class="admin-label">Слайд <?= $ri + 1 ?> — PNG</label>
                            <div class="admin-img-wrap">
                                <div class="admin-img-row">
                                    <input class="admin-input admin-img-url" name="pro_reality_spark_<?= $ri ?>" value="<?= h((string)($profState['reality_sparks'][$ri] ?? '')) ?>" placeholder="/uploads/...png">
                                    <button type="button" class="admin-btn admin-img-upload-btn" title="Загрузить">↑</button>
                                    <span class="admin-img-spin" hidden>…</span>
                                </div>
                                <input type="file" class="admin-img-file" accept="image/png" hidden>
                                <img class="admin-img-preview" src="<?= h((string)($profState['reality_sparks'][$ri] ?? '')) ?>" alt=""<?= ((string)($profState['reality_sparks'][$ri] ?? '')) === '' ? ' hidden' : '' ?>>
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </fieldset>

                <fieldset class="admin-fieldset admin-fieldset--flat">
                    <legend>Equips compare (p-2-4)</legend>
                    <label class="admin-label">Заголовок</label>
                    <input class="admin-input" name="pro_equip_title" value="<?= h((string)($profState['equip_title'] ?? '')) ?>">
                    <label class="admin-label">Подзаголовок</label>
                    <input class="admin-input" name="pro_equip_subtitle" value="<?= h((string)($profState['equip_subtitle'] ?? '')) ?>">

                    <p class="admin-hint" style="margin-top:1rem">Видео (по клику на play в центре)</p>
                    <label class="admin-label">YouTube ID или URL</label>
                    <input class="admin-input" name="pro_equip_video_yt" value="<?= h((string)($profState['equip_video_yt'] ?? '')) ?>" placeholder="dQw4w9WgXcQ">
                    <label class="admin-label">Видео MP4 URL</label>
                    <div class="admin-img-wrap">
                        <div class="admin-img-row">
                            <input class="admin-input admin-img-url" name="pro_equip_video_mp4" value="<?= h((string)($profState['equip_video_mp4'] ?? '')) ?>" placeholder="/uploads/...">
                            <button type="button" class="admin-btn admin-img-upload-btn" title="Загрузить">↑</button>
                            <span class="admin-img-spin" hidden>…</span>
                        </div>
                        <input type="file" class="admin-img-file" accept="video/mp4,video/webm" hidden>
                        <video class="admin-video-preview" src="<?= h((string)($profState['equip_video_mp4'] ?? '')) ?>" controls muted playsinline<?= ((string)($profState['equip_video_mp4'] ?? '')) === '' ? ' hidden' : '' ?>></video>
                        <img class="admin-img-preview" src="" alt="" hidden>
                    </div>
                    <label class="admin-label">Постер видео</label>
                    <div class="admin-img-wrap">
                        <div class="admin-img-row">
                            <input class="admin-input admin-img-url" name="pro_equip_video_poster" value="<?= h((string)($profState['equip_video_poster'] ?? '')) ?>" placeholder="/uploads/...">
                            <button type="button" class="admin-btn admin-img-upload-btn" title="Загрузить">↑</button>
                            <span class="admin-img-spin" hidden>…</span>
                        </div>
                        <input type="file" class="admin-img-file" accept="image/*" hidden>
                        <img class="admin-img-preview" src="<?= h((string)($profState['equip_video_poster'] ?? '')) ?>" alt=""<?= ((string)($profState['equip_video_poster'] ?? '')) === '' ? ' hidden' : '' ?>>
                    </div>
                </fieldset>

                <fieldset class="admin-fieldset admin-fieldset--flat">
                    <legend>NextGen image slider (p-2-7)</legend>
                    <label class="admin-label">Заголовок блока</label>
                    <input class="admin-input" name="pro_nextgen_title" value="<?= h((string)($profState['nextgen_title'] ?? '')) ?>">
                    <div class="admin-grid2">
                        <div>
                            <label class="admin-label">Чип слева</label>
                            <input class="admin-input" name="pro_nextgen_chip_left" value="<?= h((string)($profState['nextgen_chip_left'] ?? '')) ?>">
                        </div>
                        <div>
                            <label class="admin-label">Чип справа</label>
                            <input class="admin-input" name="pro_nextgen_chip_right" value="<?= h((string)($profState['nextgen_chip_right'] ?? '')) ?>">
                        </div>
                    </div>

                    <div class="admin-grid2" style="grid-template-columns:repeat(3,1fr)">
                        <?php for ($ni = 0; $ni < 3; $ni++): ?>
                        <div>
                            <p class="admin-hint">Слайд <?= $ni + 1 ?></p>
                            <label class="admin-label">Название таба</label>
                            <input class="admin-input" name="pro_nextgen_label_<?= $ni ?>" value="<?= h((string)($profState['nextgen_tabs'][$ni]['label'] ?? '')) ?>">
                            <label class="admin-label">Подзаголовок</label>
                            <textarea class="admin-textarea admin-textarea--sm" name="pro_nextgen_subtitle_<?= $ni ?>" rows="3"><?= h((string)($profState['nextgen_tabs'][$ni]['subtitle'] ?? '')) ?></textarea>
                            <label class="admin-label">Изображение</label>
                            <div class="admin-img-wrap">
                                <div class="admin-img-row">
                                    <input class="admin-input admin-img-url" name="pro_nextgen_image_<?= $ni ?>" value="<?= h((string)($profState['nextgen_tabs'][$ni]['image'] ?? '')) ?>" placeholder="/uploads/...">
                                    <button type="button" class="admin-btn admin-img-upload-btn" title="Загрузить">↑</button>
                                    <span class="admin-img-spin" hidden>…</span>
                                </div>
                                <input type="file" class="admin-img-file" accept="image/*" hidden>
                                <img class="admin-img-preview" src="<?= h((string)($profState['nextgen_tabs'][$ni]['image'] ?? '')) ?>" alt=""<?= ((string)($profState['nextgen_tabs'][$ni]['image'] ?? '')) === '' ? ' hidden' : '' ?>>
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </fieldset>

                <fieldset class="admin-fieldset admin-fieldset--flat">
                    <legend>Challenges glasses (p-2-8)</legend>
                    <label class="admin-label">Заголовок</label>
                    <textarea class="admin-textarea admin-textarea--sm" name="pro_challenges_title" rows="2"><?= h((string)($profState['challenges_title'] ?? '')) ?></textarea>
                    <label class="admin-label">Изображение гарнитуры</label>
                    <div class="admin-img-wrap">
                        <div class="admin-img-row">
                            <input class="admin-input admin-img-url" name="pro_challenges_device_image" value="<?= h((string)($profState['challenges_device_image'] ?? '')) ?>" placeholder="/uploads/...">
                            <button type="button" class="admin-btn admin-img-upload-btn" title="Загрузить">↑</button>
                            <span class="admin-img-spin" hidden>…</span>
                        </div>
                        <input type="file" class="admin-img-file" accept="image/*" hidden>
                        <img class="admin-img-preview" src="<?= h((string)($profState['challenges_device_image'] ?? '')) ?>" alt=""<?= ((string)($profState['challenges_device_image'] ?? '')) === '' ? ' hidden' : '' ?>>
                    </div>
                    <label class="admin-label">Подпись под гарнитурой</label>
                    <input class="admin-input" name="pro_challenges_device_caption" value="<?= h((string)($profState['challenges_device_caption'] ?? '')) ?>">
                    <label class="admin-label">PNG подписи (вместо текста)</label>
                    <div class="admin-img-wrap">
                        <div class="admin-img-row">
                            <input class="admin-input admin-img-url" name="pro_challenges_device_caption_image" value="<?= h((string)($profState['challenges_device_caption_image'] ?? '')) ?>" placeholder="/uploads/...png">
                            <button type="button" class="admin-btn admin-img-upload-btn" title="Загрузить">↑</button>
                            <span class="admin-img-spin" hidden>…</span>
                        </div>
                        <input type="file" class="admin-img-file" accept="image/png,image/webp,image/*" hidden>
                        <img class="admin-img-preview" src="<?= h((string)($profState['challenges_device_caption_image'] ?? '')) ?>" alt=""<?= ((string)($profState['challenges_device_caption_image'] ?? '')) === '' ? ' hidden' : '' ?>>
                    </div>
                </fieldset>

                <fieldset class="admin-fieldset admin-fieldset--flat">
                    <legend>Gallery (p-2-9)</legend>
                    <label class="admin-label">Заголовок галереи</label>
                    <input class="admin-input" name="pro_gallery_heading" value="<?= h($profState['gallery_heading']) ?>">
                    <div class="admin-grid2" style="grid-template-columns:repeat(3,1fr)">
                        <?php for ($si = 0; $si < 3; $si++): ?>
                        <div>
                            <label class="admin-label">Слайд <?= $si + 1 ?> — Название</label>
                            <input class="admin-input" name="pro_slide_title_<?= $si ?>" value="<?= h($profState['gallery_slides'][$si]['title']) ?>">
                            <label class="admin-label">Слайд <?= $si + 1 ?> — Изображение</label>
                            <div class="admin-img-wrap">
                                <div class="admin-img-row">
                                    <input class="admin-input admin-img-url" name="pro_slide_image_<?= $si ?>" value="<?= h($profState['gallery_slides'][$si]['image']) ?>" placeholder="/assets/img/...">
                                    <button type="button" class="admin-btn admin-img-upload-btn" title="Загрузить">↑</button>
                                    <span class="admin-img-spin" hidden>…</span>
                                </div>
                                <input type="file" class="admin-img-file" accept="image/*" hidden>
                                <img class="admin-img-preview" src="<?= h($profState['gallery_slides'][$si]['image']) ?>" alt=""<?= $profState['gallery_slides'][$si]['image'] === '' ? ' hidden' : '' ?>>
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </fieldset>

                <fieldset class="admin-fieldset admin-fieldset--flat">
                    <legend>Workflow steps (p-2-11)</legend>
                    <label class="admin-label">Заголовок секции</label>
                    <input class="admin-input" name="pro_workflow_title" value="<?= h((string)($profState['workflow_title'] ?? '')) ?>">
                    <label class="admin-label">Подзаголовок</label>
                    <input class="admin-input" name="pro_workflow_subtitle" value="<?= h((string)($profState['workflow_subtitle'] ?? '')) ?>">
                    <label class="admin-label">Нижняя строка</label>
                    <input class="admin-input" name="pro_workflow_footer" value="<?= h((string)($profState['workflow_footer'] ?? '')) ?>">

                    <div class="admin-grid2" style="grid-template-columns:repeat(2,1fr)">
                        <?php for ($wi = 0; $wi < 6; $wi++): ?>
                        <div>
                            <p class="admin-hint">Шаг <?= $wi + 1 ?></p>
                            <label class="admin-label">Короткий заголовок (например: I. Login)</label>
                            <input class="admin-input" name="pro_workflow_step_title_<?= $wi ?>" value="<?= h((string)($profState['workflow_steps'][$wi]['title'] ?? '')) ?>">
                            <label class="admin-label">Текст шага</label>
                            <textarea class="admin-textarea admin-textarea--sm" name="pro_workflow_step_text_<?= $wi ?>" rows="2"><?= h((string)($profState['workflow_steps'][$wi]['text'] ?? '')) ?></textarea>
                            <label class="admin-label">PNG шага</label>
                            <div class="admin-img-wrap">
                                <div class="admin-img-row">
                                    <input class="admin-input admin-img-url" name="pro_workflow_step_image_<?= $wi ?>" value="<?= h((string)($profState['workflow_steps'][$wi]['image'] ?? '')) ?>" placeholder="/uploads/...png">
                                    <button type="button" class="admin-btn admin-img-upload-btn" title="Загрузить">↑</button>
                                    <span class="admin-img-spin" hidden>…</span>
                                </div>
                                <input type="file" class="admin-img-file" accept="image/png,image/webp,image/*" hidden>
                                <img class="admin-img-preview" src="<?= h((string)($profState['workflow_steps'][$wi]['image'] ?? '')) ?>" alt=""<?= ((string)($profState['workflow_steps'][$wi]['image'] ?? '')) === '' ? ' hidden' : '' ?>>
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </fieldset>

                <fieldset class="admin-fieldset admin-fieldset--flat">
                    <legend>Final split banner (p-2-17)</legend>
                    <label class="admin-label">Текст справа</label>
                    <textarea class="admin-textarea admin-textarea--sm" name="pro_outro_text" rows="3"><?= h((string)($profState['outro_text'] ?? '')) ?></textarea>
                    <label class="admin-label">Изображение слева</label>
                    <div class="admin-img-wrap">
                        <div class="admin-img-row">
                            <input class="admin-input admin-img-url" name="pro_outro_image" value="<?= h((string)($profState['outro_image'] ?? '')) ?>" placeholder="/uploads/...png">
                            <button type="button" class="admin-btn admin-img-upload-btn" title="Загрузить">↑</button>
                            <span class="admin-img-spin" hidden>…</span>
                        </div>
                        <input type="file" class="admin-img-file" accept="image/*" hidden>
                        <img class="admin-img-preview" src="<?= h((string)($profState['outro_image'] ?? '')) ?>" alt=""<?= ((string)($profState['outro_image'] ?? '')) === '' ? ' hidden' : '' ?>>
                    </div>
                </fieldset>

            </div>
        </details>
    </section>

    <!-- ═══════════════════ INSTITUTIONS ═══════════════════ -->
    <section id="section-institutions" class="admin-section">
        <details class="admin-disclosure">
            <summary class="admin-disclosure__summary">Institutions — блоки страницы</summary>
            <div class="admin-disclosure__body">

                <fieldset class="admin-fieldset admin-fieldset--flat">
                    <legend>Carousel Hero (i-3-1)</legend>
                    <label class="admin-label">Заголовок</label>
                    <input class="admin-input" name="inst_carousel_heading" value="<?= h($instState['carousel_heading']) ?>">
                    <div class="admin-grid2" style="grid-template-columns:repeat(3,1fr)">
                        <?php for ($si = 0; $si < 3; $si++): ?>
                        <div>
                            <label class="admin-label">Слайд <?= $si + 1 ?> — Название</label>
                            <input class="admin-input" name="inst_carousel_title_<?= $si ?>" value="<?= h($instState['carousel_slides'][$si]['title']) ?>">
                            <label class="admin-label">Слайд <?= $si + 1 ?> — Изображение</label>
                            <div class="admin-img-wrap">
                                <div class="admin-img-row">
                                    <input class="admin-input admin-img-url" name="inst_carousel_image_<?= $si ?>" value="<?= h($instState['carousel_slides'][$si]['image']) ?>" placeholder="/assets/img/...">
                                    <button type="button" class="admin-btn admin-img-upload-btn" title="Загрузить">↑</button>
                                    <span class="admin-img-spin" hidden>…</span>
                                </div>
                                <input type="file" class="admin-img-file" accept="image/*" hidden>
                                <img class="admin-img-preview" src="<?= h($instState['carousel_slides'][$si]['image']) ?>" alt=""<?= $instState['carousel_slides'][$si]['image'] === '' ? ' hidden' : '' ?>>
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </fieldset>

                <fieldset class="admin-fieldset admin-fieldset--flat">
                    <legend>Feature masonry (i-3-10) — тёмные карточки</legend>
                    <p class="admin-hint">Картинки для блоков Diagnostic (кол. 1) и Tele-Mentoring (кол. 2). Можно вставить URL или загрузить файл (↑).</p>
                    <div class="admin-grid2">
                        <div>
                            <label class="admin-label">Diagnostic — изображение</label>
                            <div class="admin-img-wrap">
                                <div class="admin-img-row">
                                    <input class="admin-input admin-img-url" name="inst_i310_image_a" value="<?= h((string)($instState['i310_image_a'] ?? '')) ?>" placeholder="/uploads/... или /assets/img/figma/institutions/inst-310a.webp">
                                    <button type="button" class="admin-btn admin-img-upload-btn" title="Загрузить">↑</button>
                                    <span class="admin-img-spin" hidden>…</span>
                                </div>
                                <input type="file" class="admin-img-file" accept="image/*" hidden>
                                <img class="admin-img-preview" src="<?= h((string)($instState['i310_image_a'] ?? '')) ?>" alt=""<?= ((string)($instState['i310_image_a'] ?? '')) === '' ? ' hidden' : '' ?>>
                            </div>
                        </div>
                        <div>
                            <label class="admin-label">Tele-Mentoring — изображение</label>
                            <div class="admin-img-wrap">
                                <div class="admin-img-row">
                                    <input class="admin-input admin-img-url" name="inst_i310_image_b" value="<?= h((string)($instState['i310_image_b'] ?? '')) ?>" placeholder="/uploads/... или /assets/img/figma/institutions/inst-310b.webp">
                                    <button type="button" class="admin-btn admin-img-upload-btn" title="Загрузить">↑</button>
                                    <span class="admin-img-spin" hidden>…</span>
                                </div>
                                <input type="file" class="admin-img-file" accept="image/*" hidden>
                                <img class="admin-img-preview" src="<?= h((string)($instState['i310_image_b'] ?? '')) ?>" alt=""<?= ((string)($instState['i310_image_b'] ?? '')) === '' ? ' hidden' : '' ?>>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="admin-fieldset admin-fieldset--flat">
                    <legend>Next-Gen Hands-On Training (i-3-18)</legend>
                    <p class="admin-hint">Инфографика: заголовок слева, карточка справа. Четыре изображения модулей и центр (гарнитура) — URL или загрузка (↑). Розовая сетка из макета в вёрстку не входит.</p>
                    <label class="admin-label">Заголовок — строка 1</label>
                    <input class="admin-input" name="inst_nx_title_l0" value="<?= h((string)($instState['nx_title_l0'] ?? '')) ?>">
                    <label class="admin-label">Заголовок — строка 2</label>
                    <input class="admin-input" name="inst_nx_title_l1" value="<?= h((string)($instState['nx_title_l1'] ?? '')) ?>">
                    <label class="admin-label">Заголовок — строка 3</label>
                    <input class="admin-input" name="inst_nx_title_l2" value="<?= h((string)($instState['nx_title_l2'] ?? '')) ?>">
                    <div class="admin-grid2" style="margin-top:12px">
                        <div>
                            <label class="admin-label">Навигация — пункт 1 (под ним градиентная линия)</label>
                            <input class="admin-input" name="inst_nx_nav_0" value="<?= h((string)($instState['nx_nav_0'] ?? '')) ?>">
                        </div>
                        <div>
                            <label class="admin-label">Навигация — пункт 2</label>
                            <input class="admin-input" name="inst_nx_nav_1" value="<?= h((string)($instState['nx_nav_1'] ?? '')) ?>">
                        </div>
                    </div>
                    <label class="admin-label">Навигация — пункт 3</label>
                    <input class="admin-input" name="inst_nx_nav_2" value="<?= h((string)($instState['nx_nav_2'] ?? '')) ?>">
                    <label class="admin-label">Заголовок в карточке (AR/VR)</label>
                    <input class="admin-input" name="inst_nx_card_heading" value="<?= h((string)($instState['nx_card_heading'] ?? '')) ?>">
                    <label class="admin-label">Центр — подпись на визоре (бренд)</label>
                    <input class="admin-input" name="inst_nx_center_brand" value="<?= h((string)($instState['nx_center_brand'] ?? '')) ?>">
                    <label class="admin-label">Центр — подпись под изображением</label>
                    <input class="admin-input" name="inst_nx_center_label" value="<?= h((string)($instState['nx_center_label'] ?? '')) ?>">
                    <label class="admin-label">Центр — изображение (гарнитура)</label>
                    <div class="admin-img-wrap">
                        <div class="admin-img-row">
                            <input class="admin-input admin-img-url" name="inst_nx_center_image" value="<?= h((string)($instState['nx_center_image'] ?? '')) ?>" placeholder="/uploads/...">
                            <button type="button" class="admin-btn admin-img-upload-btn" title="Загрузить">↑</button>
                            <span class="admin-img-spin" hidden>…</span>
                        </div>
                        <input type="file" class="admin-img-file" accept="image/*" hidden>
                        <img class="admin-img-preview" src="<?= h((string)($instState['nx_center_image'] ?? '')) ?>" alt=""<?= ((string)($instState['nx_center_image'] ?? '')) === '' ? ' hidden' : '' ?>>
                    </div>
                    <p class="admin-hint" style="margin-top:16px">Модули карточки (сверху-слева → сверху-справа → снизу-слева → снизу-справа):</p>
                    <div class="admin-grid2" style="grid-template-columns:repeat(2,1fr)">
                        <?php
                        $nxMods = $instState['nx_modules'] ?? [];
                        $nxSlotNames = ['Вверху слева', 'Вверху справа', 'Внизу слева', 'Внизу справа'];
                        for ($nxi = 0; $nxi < 4; $nxi++):
                            $nm = is_array($nxMods[$nxi] ?? null) ? $nxMods[$nxi] : ['label' => '', 'image' => ''];
                            ?>
                        <div>
                            <label class="admin-label"><?= h($nxSlotNames[$nxi]) ?> — подпись</label>
                            <input class="admin-input" name="inst_nx_mod_<?= $nxi ?>_label" value="<?= h((string)($nm['label'] ?? '')) ?>">
                            <label class="admin-label"><?= h($nxSlotNames[$nxi]) ?> — изображение</label>
                            <div class="admin-img-wrap">
                                <div class="admin-img-row">
                                    <input class="admin-input admin-img-url" name="inst_nx_mod_<?= $nxi ?>_image" value="<?= h((string)($nm['image'] ?? '')) ?>" placeholder="/uploads/...">
                                    <button type="button" class="admin-btn admin-img-upload-btn" title="Загрузить">↑</button>
                                    <span class="admin-img-spin" hidden>…</span>
                                </div>
                                <input type="file" class="admin-img-file" accept="image/*" hidden>
                                <img class="admin-img-preview" src="<?= h((string)($nm['image'] ?? '')) ?>" alt=""<?= ((string)($nm['image'] ?? '')) === '' ? ' hidden' : '' ?>>
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </fieldset>

                <fieldset class="admin-fieldset admin-fieldset--flat">
                    <legend>Gallery (i-3-21-24)</legend>
                    <label class="admin-label">Заголовок галереи</label>
                    <input class="admin-input" name="inst_gallery_heading" value="<?= h($instState['gallery_heading']) ?>">
                    <div class="admin-grid2" style="grid-template-columns:repeat(3,1fr)">
                        <?php for ($si = 0; $si < 3; $si++): ?>
                        <div>
                            <label class="admin-label">Слайд <?= $si + 1 ?> — Название</label>
                            <input class="admin-input" name="inst_gallery_title_<?= $si ?>" value="<?= h($instState['gallery_slides'][$si]['title']) ?>">
                            <label class="admin-label">Слайд <?= $si + 1 ?> — Изображение</label>
                            <div class="admin-img-wrap">
                                <div class="admin-img-row">
                                    <input class="admin-input admin-img-url" name="inst_gallery_image_<?= $si ?>" value="<?= h($instState['gallery_slides'][$si]['image']) ?>" placeholder="/assets/img/...">
                                    <button type="button" class="admin-btn admin-img-upload-btn" title="Загрузить">↑</button>
                                    <span class="admin-img-spin" hidden>…</span>
                                </div>
                                <input type="file" class="admin-img-file" accept="image/*" hidden>
                                <img class="admin-img-preview" src="<?= h($instState['gallery_slides'][$si]['image']) ?>" alt=""<?= $instState['gallery_slides'][$si]['image'] === '' ? ' hidden' : '' ?>>
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </fieldset>

                <fieldset class="admin-fieldset admin-fieldset--flat">
                    <legend>Care slider (i-3-4)</legend>
                    <label class="admin-label">Заголовок</label>
                    <input class="admin-input" name="inst_care_title" value="<?= h((string)($instState['care_title'] ?? '')) ?>">
                    <div class="admin-grid2">
                        <div>
                            <label class="admin-label">Чип 1</label>
                            <input class="admin-input" name="inst_care_chip_a" value="<?= h((string)($instState['care_chip_a'] ?? '')) ?>">
                        </div>
                        <div>
                            <label class="admin-label">Чип 2</label>
                            <input class="admin-input" name="inst_care_chip_b" value="<?= h((string)($instState['care_chip_b'] ?? '')) ?>">
                        </div>
                    </div>
                    <div class="admin-grid2" style="grid-template-columns:repeat(3,1fr)">
                        <?php for ($ci = 0; $ci < 3; $ci++): ?>
                        <div>
                            <label class="admin-label">Слайд <?= $ci + 1 ?> — Подзаголовок</label>
                            <input class="admin-input" name="inst_care_subtitle_<?= $ci ?>" value="<?= h((string)($instState['care_slides'][$ci]['subtitle'] ?? '')) ?>">
                            <label class="admin-label">Слайд <?= $ci + 1 ?> — Изображение</label>
                            <div class="admin-img-wrap">
                                <div class="admin-img-row">
                                    <input class="admin-input admin-img-url" name="inst_care_image_<?= $ci ?>" value="<?= h((string)($instState['care_slides'][$ci]['image'] ?? '')) ?>" placeholder="/uploads/...">
                                    <button type="button" class="admin-btn admin-img-upload-btn" title="Загрузить">↑</button>
                                    <span class="admin-img-spin" hidden>…</span>
                                </div>
                                <input type="file" class="admin-img-file" accept="image/*" hidden>
                                <img class="admin-img-preview" src="<?= h((string)($instState['care_slides'][$ci]['image'] ?? '')) ?>" alt=""<?= ((string)($instState['care_slides'][$ci]['image'] ?? '')) === '' ? ' hidden' : '' ?>>
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </fieldset>

                <fieldset class="admin-fieldset admin-fieldset--flat">
                    <legend>Before/After (i-3-6)</legend>
                    <label class="admin-label">Заголовок поверх изображения (h2)</label>
                    <input class="admin-input" name="inst_ba_overlay_title" value="<?= h((string)($instState['ba_overlay_title'] ?? '')) ?>">
                    <label class="admin-label">Текст поверх изображения (абзац)</label>
                    <textarea class="admin-input" name="inst_ba_overlay_text" rows="3"><?= h((string)($instState['ba_overlay_text'] ?? '')) ?></textarea>
                    <div class="admin-grid2">
                        <div>
                            <label class="admin-label">Before — изображение</label>
                            <div class="admin-img-wrap">
                                <div class="admin-img-row">
                                    <input class="admin-input admin-img-url" name="inst_ba_before_image" value="<?= h((string)($instState['ba_before_image'] ?? '')) ?>" placeholder="/uploads/...">
                                    <button type="button" class="admin-btn admin-img-upload-btn" title="Загрузить">↑</button>
                                    <span class="admin-img-spin" hidden>…</span>
                                </div>
                                <input type="file" class="admin-img-file" accept="image/*" hidden>
                                <img class="admin-img-preview" src="<?= h((string)($instState['ba_before_image'] ?? '')) ?>" alt=""<?= ((string)($instState['ba_before_image'] ?? '')) === '' ? ' hidden' : '' ?>>
                            </div>
                        </div>
                        <div>
                            <label class="admin-label">After — изображение</label>
                            <div class="admin-img-wrap">
                                <div class="admin-img-row">
                                    <input class="admin-input admin-img-url" name="inst_ba_after_image" value="<?= h((string)($instState['ba_after_image'] ?? '')) ?>" placeholder="/uploads/...">
                                    <button type="button" class="admin-btn admin-img-upload-btn" title="Загрузить">↑</button>
                                    <span class="admin-img-spin" hidden>…</span>
                                </div>
                                <input type="file" class="admin-img-file" accept="image/*" hidden>
                                <img class="admin-img-preview" src="<?= h((string)($instState['ba_after_image'] ?? '')) ?>" alt=""<?= ((string)($instState['ba_after_image'] ?? '')) === '' ? ' hidden' : '' ?>>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="admin-fieldset admin-fieldset--flat">
                    <legend>Discover banner (i-3-7)</legend>
                    <p class="admin-hint">Три строки заголовка по центру на градиентном фоне.</p>
                    <label class="admin-label">Строка 1</label>
                    <input class="admin-input" name="inst_discover_line_1" value="<?= h((string)($instState['discover_line_1'] ?? '')) ?>">
                    <label class="admin-label">Строка 2</label>
                    <input class="admin-input" name="inst_discover_line_2" value="<?= h((string)($instState['discover_line_2'] ?? '')) ?>">
                    <label class="admin-label">Строка 3</label>
                    <input class="admin-input" name="inst_discover_line_3" value="<?= h((string)($instState['discover_line_3'] ?? '')) ?>">
                </fieldset>

                <fieldset class="admin-fieldset admin-fieldset--flat">
                    <legend>Orbit Assistant (i-3-8)</legend>
                    <?php
                    $orbitL = is_array($instState['orbit_label'] ?? null) ? $instState['orbit_label'] : array_fill(0, 8, '');
                    ?>
                    <p class="admin-hint">Текст слева, орбита справа. Центральное изображение — круг в блоке орбиты.</p>
                    <label class="admin-label">Текст слева в режиме stack (если не заданы поля Assistant выше — две колонки как в Page Builder)</label>
                    <textarea class="admin-textarea admin-textarea--sm" name="inst_orbit_stack_description" rows="3" placeholder="Короткий абзац под заголовком"><?= h((string)($instState['orbit_stack_description'] ?? '')) ?></textarea>
                    <label class="admin-label">Заголовок (градиент)</label>
                    <input class="admin-input" name="inst_orbit_intro_title" value="<?= h((string)($instState['orbit_intro_title'] ?? '')) ?>">
                    <label class="admin-label">Подзаголовок (несколько строк — Enter для переноса)</label>
                    <textarea class="admin-textarea admin-textarea--sm" name="inst_orbit_intro_subtitle" rows="3"><?= h((string)($instState['orbit_intro_subtitle'] ?? '')) ?></textarea>
                    <label class="admin-label">Абзац</label>
                    <textarea class="admin-input" name="inst_orbit_intro_body" rows="3"><?= h((string)($instState['orbit_intro_body'] ?? '')) ?></textarea>
                    <div class="admin-grid2">
                        <div>
                            <label class="admin-label">Карточка 1 — заголовок</label>
                            <input class="admin-input" name="inst_orbit_f0_title" value="<?= h((string)($instState['orbit_f0_title'] ?? '')) ?>">
                            <label class="admin-label">Карточка 1 — текст</label>
                            <textarea class="admin-textarea admin-textarea--sm" name="inst_orbit_f0_text" rows="2"><?= h((string)($instState['orbit_f0_text'] ?? '')) ?></textarea>
                        </div>
                        <div>
                            <label class="admin-label">Карточка 2 — заголовок</label>
                            <input class="admin-input" name="inst_orbit_f1_title" value="<?= h((string)($instState['orbit_f1_title'] ?? '')) ?>">
                            <label class="admin-label">Карточка 2 — текст</label>
                            <textarea class="admin-textarea admin-textarea--sm" name="inst_orbit_f1_text" rows="2"><?= h((string)($instState['orbit_f1_text'] ?? '')) ?></textarea>
                        </div>
                    </div>
                    <label class="admin-label">Подпись внутри круга под фото (напр. XR DOCTOR)</label>
                    <input class="admin-input" name="inst_orbit_center_label" value="<?= h((string)($instState['orbit_center_label'] ?? '')) ?>">
                    <label class="admin-label">Иконка слева от меток на орбите (URL)</label>
                    <input class="admin-input admin-img-url" name="inst_orbit_pill_label_icon" value="<?= h((string)($instState['orbit_pill_label_icon'] ?? '')) ?>" placeholder="/uploads/...">
                    <label class="admin-label">Фото в центре круга</label>
                    <div class="admin-img-wrap">
                        <div class="admin-img-row">
                            <input class="admin-input admin-img-url" name="inst_orbit_center_image" value="<?= h((string)($instState['orbit_center_image'] ?? '')) ?>" placeholder="/uploads/...">
                            <button type="button" class="admin-btn admin-img-upload-btn" title="Загрузить">↑</button>
                            <span class="admin-img-spin" hidden>…</span>
                        </div>
                        <input type="file" class="admin-img-file" accept="image/*" hidden>
                        <img class="admin-img-preview" src="<?= h((string)($instState['orbit_center_image'] ?? '')) ?>" alt=""<?= ((string)($instState['orbit_center_image'] ?? '')) === '' ? ' hidden' : '' ?>>
                    </div>
                    <p class="admin-hint" style="margin-top:1rem">Подписи на орбите (по часовой, до 8)</p>
                    <div class="admin-grid2" style="grid-template-columns:repeat(4,1fr)">
                        <?php for ($oi = 0; $oi < 8; $oi++): ?>
                        <div>
                            <label class="admin-label">Метка <?= $oi + 1 ?></label>
                            <input class="admin-input" name="inst_orbit_label_<?= $oi ?>" value="<?= h((string)($orbitL[$oi] ?? '')) ?>" placeholder="Label">
                        </div>
                        <?php endfor; ?>
                    </div>
                </fieldset>

                <fieldset class="admin-fieldset admin-fieldset--flat">
                    <legend>Advantages — орбита (i-3-11)</legend>
                    <p class="admin-hint">Тёмная секция с орбитой преимуществ. Центр и иконка меток задаются здесь; блок Orbit Assistant (i-3-8) — отдельная орбита выше по странице.</p>
                    <label class="admin-label">Подпись внутри круга под фото</label>
                    <input class="admin-input" name="inst_orbit_adv_center_label" value="<?= h((string)($instState['orbit_adv_center_label'] ?? '')) ?>">
                    <label class="admin-label">Иконка слева от меток на орбите</label>
                    <div class="admin-img-wrap">
                        <div class="admin-img-row">
                            <input class="admin-input admin-img-url" name="inst_orbit_adv_pill_label_icon" value="<?= h((string)($instState['orbit_adv_pill_label_icon'] ?? '')) ?>" placeholder="/uploads/...">
                            <button type="button" class="admin-btn admin-img-upload-btn" title="Загрузить">↑</button>
                            <span class="admin-img-spin" hidden>…</span>
                        </div>
                        <input type="file" class="admin-img-file" accept="image/*" hidden>
                        <img class="admin-img-preview" src="<?= h((string)($instState['orbit_adv_pill_label_icon'] ?? '')) ?>" alt=""<?= ((string)($instState['orbit_adv_pill_label_icon'] ?? '')) === '' ? ' hidden' : '' ?>>
                    </div>
                    <label class="admin-label">Фото в центре круга</label>
                    <div class="admin-img-wrap">
                        <div class="admin-img-row">
                            <input class="admin-input admin-img-url" name="inst_orbit_adv_center_image" value="<?= h((string)($instState['orbit_adv_center_image'] ?? '')) ?>" placeholder="/uploads/...">
                            <button type="button" class="admin-btn admin-img-upload-btn" title="Загрузить">↑</button>
                            <span class="admin-img-spin" hidden>…</span>
                        </div>
                        <input type="file" class="admin-img-file" accept="image/*" hidden>
                        <img class="admin-img-preview" src="<?= h((string)($instState['orbit_adv_center_image'] ?? '')) ?>" alt=""<?= ((string)($instState['orbit_adv_center_image'] ?? '')) === '' ? ' hidden' : '' ?>>
                    </div>
                    <label class="admin-label">Картинка на вершине внешнего кольца</label>
                    <div class="admin-img-wrap">
                        <div class="admin-img-row">
                            <input class="admin-input admin-img-url" name="inst_orbit_adv_outer_apex_image" value="<?= h((string)($instState['orbit_adv_outer_apex_image'] ?? '')) ?>" placeholder="/uploads/...">
                            <button type="button" class="admin-btn admin-img-upload-btn" title="Загрузить">↑</button>
                            <span class="admin-img-spin" hidden>…</span>
                        </div>
                        <input type="file" class="admin-img-file" accept="image/*" hidden>
                        <img class="admin-img-preview" src="<?= h((string)($instState['orbit_adv_outer_apex_image'] ?? '')) ?>" alt=""<?= ((string)($instState['orbit_adv_outer_apex_image'] ?? '')) === '' ? ' hidden' : '' ?>>
                    </div>
                </fieldset>

            </div>
        </details>
    </section>

    <!-- ═══════════════════ BLOG ═══════════════════ -->
    <section id="section-blog" class="admin-section">
        <details class="admin-disclosure">
            <summary class="admin-disclosure__summary">Blog — блоки страницы</summary>
            <div class="admin-disclosure__body">

                <fieldset class="admin-fieldset admin-fieldset--flat">
                    <legend>Hero (block-4-1)</legend>
                    <label class="admin-label">Заголовок</label>
                    <input class="admin-input" name="blog_hero_title" value="<?= h($blogState['hero_title']) ?>">
                    <label class="admin-label">Подзаголовок</label>
                    <input class="admin-input" name="blog_hero_subtitle" value="<?= h($blogState['hero_subtitle']) ?>">
                    <label class="admin-label">Изображение</label>
                    <div class="admin-img-wrap">
                        <div class="admin-img-row">
                            <input class="admin-input admin-img-url" name="blog_hero_image" value="<?= h($blogState['hero_image']) ?>" placeholder="/assets/img/...">
                            <button type="button" class="admin-btn admin-img-upload-btn" title="Загрузить">↑</button>
                            <span class="admin-img-spin" hidden>…</span>
                        </div>
                        <input type="file" class="admin-img-file" accept="image/*" hidden>
                        <img class="admin-img-preview" src="<?= h($blogState['hero_image']) ?>" alt=""<?= $blogState['hero_image'] === '' ? ' hidden' : '' ?>>
                    </div>
                </fieldset>

                <fieldset class="admin-fieldset admin-fieldset--flat">
                    <legend>Пости (block-4-2-grid)</legend>
                    <?php for ($pi = 0; $pi < 5; $pi++): ?>
                    <details class="admin-disclosure admin-disclosure--nested">
                        <summary class="admin-disclosure__summary">Пост <?= $pi + 1 ?><?= $blogState['posts'][$pi]['title'] !== '' ? ' — ' . h($blogState['posts'][$pi]['title']) : '' ?></summary>
                        <div class="admin-disclosure__body">
                            <label class="admin-label">Заголовок</label>
                            <input class="admin-input" name="blog_post_title_<?= $pi ?>" value="<?= h($blogState['posts'][$pi]['title']) ?>">
                            <label class="admin-label">Анонс</label>
                            <textarea class="admin-textarea admin-textarea--sm" name="blog_post_excerpt_<?= $pi ?>" rows="3"><?= h($blogState['posts'][$pi]['excerpt']) ?></textarea>
                            <label class="admin-label">Изображение</label>
                            <div class="admin-img-wrap">
                                <div class="admin-img-row">
                                    <input class="admin-input admin-img-url" name="blog_post_image_<?= $pi ?>" value="<?= h($blogState['posts'][$pi]['image']) ?>" placeholder="/assets/img/...">
                                    <button type="button" class="admin-btn admin-img-upload-btn" title="Загрузить">↑</button>
                                    <span class="admin-img-spin" hidden>…</span>
                                </div>
                                <input type="file" class="admin-img-file" accept="image/*" hidden>
                                <img class="admin-img-preview" src="<?= h($blogState['posts'][$pi]['image']) ?>" alt=""<?= $blogState['posts'][$pi]['image'] === '' ? ' hidden' : '' ?>>
                            </div>
                        </div>
                    </details>
                    <?php endfor; ?>
                </fieldset>

            </div>
        </details>
    </section>

    <!-- ═══════════════════ PARTNERS ═══════════════════ -->
    <section id="section-partners" class="admin-section">
        <details class="admin-disclosure">
            <summary class="admin-disclosure__summary">Partners — блоки страницы</summary>
            <div class="admin-disclosure__body">

                <fieldset class="admin-fieldset admin-fieldset--flat">
                    <legend>Hero (block-5-1)</legend>
                    <label class="admin-label">Заголовок</label>
                    <input class="admin-input" name="par_hero_title" value="<?= h($parState['hero_title']) ?>">
                    <label class="admin-label">Подзаголовок</label>
                    <input class="admin-input" name="par_hero_subtitle" value="<?= h($parState['hero_subtitle']) ?>">
                    <label class="admin-label">Изображение</label>
                    <div class="admin-img-wrap">
                        <div class="admin-img-row">
                            <input class="admin-input admin-img-url" name="par_hero_image" value="<?= h($parState['hero_image']) ?>" placeholder="/assets/img/...">
                            <button type="button" class="admin-btn admin-img-upload-btn" title="Загрузить">↑</button>
                            <span class="admin-img-spin" hidden>…</span>
                        </div>
                        <input type="file" class="admin-img-file" accept="image/*" hidden>
                        <img class="admin-img-preview" src="<?= h($parState['hero_image']) ?>" alt=""<?= $parState['hero_image'] === '' ? ' hidden' : '' ?>>
                    </div>
                </fieldset>

                <fieldset class="admin-fieldset admin-fieldset--flat">
                    <legend>Split Video 1 (block-5-4)</legend>
                    <label class="admin-label">Заголовок</label>
                    <input class="admin-input" name="par_sv1_title" value="<?= h($parState['sv1_title']) ?>">
                    <label class="admin-label">Текст</label>
                    <textarea class="admin-textarea admin-textarea--sm" name="par_sv1_body" rows="3"><?= h($parState['sv1_body']) ?></textarea>
                    <label class="admin-label">Постер (зображення превью)</label>
                    <div class="admin-img-wrap">
                        <div class="admin-img-row">
                            <input class="admin-input admin-img-url" name="par_sv1_poster" value="<?= h($parState['sv1_poster']) ?>" placeholder="/assets/img/...">
                            <button type="button" class="admin-btn admin-img-upload-btn" title="Загрузить">↑</button>
                            <span class="admin-img-spin" hidden>…</span>
                        </div>
                        <input type="file" class="admin-img-file" accept="image/*" hidden>
                        <img class="admin-img-preview" src="<?= h($parState['sv1_poster']) ?>" alt=""<?= $parState['sv1_poster'] === '' ? ' hidden' : '' ?>>
                    </div>
                    <label class="admin-label">Відео MP4 URL</label>
                    <input class="admin-input" name="par_sv1_mp4" value="<?= h($parState['sv1_mp4']) ?>" placeholder="/uploads/...">
                </fieldset>

                <fieldset class="admin-fieldset admin-fieldset--flat">
                    <legend>Split Video 2 (block-5-8)</legend>
                    <label class="admin-label">Заголовок</label>
                    <input class="admin-input" name="par_sv2_title" value="<?= h($parState['sv2_title']) ?>">
                    <label class="admin-label">Текст</label>
                    <textarea class="admin-textarea admin-textarea--sm" name="par_sv2_body" rows="3"><?= h($parState['sv2_body']) ?></textarea>
                    <label class="admin-label">Постер (зображення превью)</label>
                    <div class="admin-img-wrap">
                        <div class="admin-img-row">
                            <input class="admin-input admin-img-url" name="par_sv2_poster" value="<?= h($parState['sv2_poster']) ?>" placeholder="/assets/img/...">
                            <button type="button" class="admin-btn admin-img-upload-btn" title="Загрузить">↑</button>
                            <span class="admin-img-spin" hidden>…</span>
                        </div>
                        <input type="file" class="admin-img-file" accept="image/*" hidden>
                        <img class="admin-img-preview" src="<?= h($parState['sv2_poster']) ?>" alt=""<?= $parState['sv2_poster'] === '' ? ' hidden' : '' ?>>
                    </div>
                    <label class="admin-label">Відео MP4 URL</label>
                    <input class="admin-input" name="par_sv2_mp4" value="<?= h($parState['sv2_mp4']) ?>" placeholder="/uploads/...">
                </fieldset>

                <fieldset class="admin-fieldset admin-fieldset--flat">
                    <legend>Partner Icons (block-5-5)</legend>
                    <label class="admin-label">Заголовок секции</label>
                    <input class="admin-input" name="par_icons_title" value="<?= h($parState['icons_title']) ?>">
                    <?php for ($ii = 0; $ii < 4; $ii++): ?>
                    <p class="admin-hint" style="margin-top:1rem">Іконка <?= $ii + 1 ?></p>
                    <div class="admin-grid2">
                        <div>
                            <label class="admin-label">Название</label>
                            <input class="admin-input" name="par_icon_label_<?= $ii ?>" value="<?= h($parState['icon_items'][$ii]['label']) ?>">
                            <label class="admin-label">Клас іконки (dashicon)</label>
                            <input class="admin-input" name="par_icon_dashicon_<?= $ii ?>" value="<?= h($parState['icon_items'][$ii]['dashicon']) ?>">
                        </div>
                        <div>
                            <label class="admin-label">Текст</label>
                            <textarea class="admin-textarea admin-textarea--sm" name="par_icon_text_<?= $ii ?>" rows="2"><?= h($parState['icon_items'][$ii]['text']) ?></textarea>
                        </div>
                    </div>
                    <?php endfor; ?>
                </fieldset>

            </div>
        </details>
    </section>

    <section id="section-json" class="admin-section">
        <details class="admin-disclosure">
            <summary class="admin-disclosure__summary"><?= h(admin_t('section.json')) ?></summary>
            <div class="admin-disclosure__body">
                <p class="admin-hint"><?= h(admin_t('hint.json')) ?></p>
                <label class="admin-label"><?= h(admin_t('json.home')) ?></label>
                <textarea class="admin-textarea admin-textarea--code" name="home_blocks_json" rows="8" spellcheck="false" placeholder="[]"></textarea>
                <label class="admin-label"><?= h(admin_t('json.pro')) ?></label>
                <textarea class="admin-textarea admin-textarea--code" name="professionals_blocks_json" rows="8" spellcheck="false" placeholder="[]"></textarea>
                <label class="admin-label"><?= h(admin_t('json.inst')) ?></label>
                <textarea class="admin-textarea admin-textarea--code" name="institutions_blocks_json" rows="8" spellcheck="false" placeholder="[]"></textarea>
                <label class="admin-label"><?= h(admin_t('json.blog')) ?></label>
                <textarea class="admin-textarea admin-textarea--code" name="blog_blocks_json" rows="8" spellcheck="false" placeholder="[]"></textarea>
                <label class="admin-label"><?= h(admin_t('json.partners')) ?></label>
                <textarea class="admin-textarea admin-textarea--code" name="partners_blocks_json" rows="8" spellcheck="false" placeholder="[]"></textarea>
            </div>
        </details>
    </section>

    <div class="admin-form__actions">
        <button class="admin-btn admin-btn--primary" type="submit"><?= h(admin_t('btn.save')) ?></button>
    </div>
</form>
<script>
(function () {
    var csrf = <?= json_encode($token) ?>;

    function initImgUpload(wrap) {
        var inp   = wrap.querySelector('.admin-img-url');
        var btn   = wrap.querySelector('.admin-img-upload-btn');
        var file  = wrap.querySelector('.admin-img-file');
        var prev  = wrap.querySelector('.admin-img-preview');
        var vprev = wrap.querySelector('.admin-video-preview');
        var spin  = wrap.querySelector('.admin-img-spin');
        if (!inp || !btn || !file) return;

        function setPreview(url) {
            var isImg = !!(url && /\.(jpe?g|png|webp|gif|svg)(\?|$)/i.test(url));
            var isVid = !!(url && /\.(mp4|webm)(\?|$)/i.test(url));
            if (prev) {
                if (isImg) {
                    prev.src = url;
                    prev.hidden = false;
                } else {
                    prev.hidden = true;
                }
            }
            if (vprev) {
                if (isVid) {
                    vprev.src = url;
                    vprev.hidden = false;
                } else {
                    vprev.hidden = true;
                }
            }
        }

        setPreview(inp.value);
        inp.addEventListener('input', function () { setPreview(inp.value); });

        btn.addEventListener('click', function () { file.click(); });

        file.addEventListener('change', function () {
            var f = file.files && file.files[0];
            if (!f) return;
            var fd = new FormData();
            fd.append('csrf', csrf);
            fd.append('file', f);
            btn.disabled = true;
            if (spin) spin.hidden = false;
            fetch('/admin/media_upload_ajax.php', { method: 'POST', body: fd })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.url) {
                        inp.value = data.url;
                        setPreview(data.url);
                    } else {
                        alert(data.error || 'Upload failed');
                    }
                })
                .catch(function () { alert('Network error during upload'); })
                .finally(function () {
                    btn.disabled = false;
                    if (spin) spin.hidden = true;
                    file.value = '';
                });
        });
    }

    document.querySelectorAll('.admin-img-wrap').forEach(initImgUpload);
})();
</script>
</body>
</html>
