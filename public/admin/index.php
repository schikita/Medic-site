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
$closing = xr_find_block_props($site['home']['blocks'] ?? [], 'closing_block');
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
    <a class="admin-toc__link" href="#section-home"><?= h(admin_t('toc.home')) ?></a>
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
                    <label class="admin-label"><?= h(admin_t('hero.poster')) ?></label>
                    <input class="admin-input" name="hero_poster" value="<?= h((string) ($hero['poster'] ?? '')) ?>">
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
                    <label class="admin-label"><?= h(admin_t('intro.line1')) ?></label>
                    <input class="admin-input" name="intro_headline_line1" value="<?= h((string) ($intro['headline_line1'] ?? '')) ?>">
                    <label class="admin-label"><?= h(admin_t('intro.line2')) ?></label>
                    <input class="admin-input" name="intro_headline_line2" value="<?= h((string) ($intro['headline_line2'] ?? '')) ?>">
                    <label class="admin-label"><?= h(admin_t('intro.tagline')) ?></label>
                    <textarea class="admin-textarea" name="intro_tagline" rows="2"><?= h((string) ($intro['tagline'] ?? '')) ?></textarea>
                </fieldset>

                <fieldset class="admin-fieldset admin-fieldset--flat">
                    <legend><?= h(admin_t('assistant.legend')) ?></legend>
                    <p class="admin-hint"><?= h(admin_t('hint.assistant')) ?></p>
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
                    <legend><?= h(admin_t('closing.legend')) ?></legend>
                    <label class="admin-label"><?= h(admin_t('closing.line1')) ?></label>
                    <input class="admin-input" name="closing_line1" value="<?= h((string) ($closing['line1'] ?? '')) ?>">
                    <label class="admin-label"><?= h(admin_t('closing.line2')) ?></label>
                    <input class="admin-input" name="closing_line2" value="<?= h((string) ($closing['line2'] ?? '')) ?>">
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
</body>
</html>
