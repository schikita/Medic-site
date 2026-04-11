<?php

declare(strict_types=1);

define('ROOT', dirname(__DIR__));
define('DATA_PATH', ROOT . '/data/site.json');
define('CONFIG_PATH', ROOT . '/config.local.php');

require_once __DIR__ . '/site_defaults.php';
require_once __DIR__ . '/site_db.php';
require_once __DIR__ . '/seo.php';

session_start();

require_once __DIR__ . '/admin_i18n.php';

function site_data_path(): string
{
    return DATA_PATH;
}

function load_site(): array
{
    $def = default_site();
    if (xr_site_uses_database()) {
        try {
            xr_site_db_ensure_installed();
            $site = xr_db_load_site_merged($def, xr_db());
        } catch (Throwable $e) {
            $site = load_site_from_json_only($def);
        }
    } else {
        $site = load_site_from_json_only($def);
    }
    $site = xr_site_merge_blocks_with_defaults($def, $site);
    $site = xr_site_heal_missing_upload_urls($site);

    return xr_normalize_site_for_seo($site);
}

function migrate_legacy_to_full(array $raw): array
{
    $def = default_site();
    $m = array_replace_recursive($def, $raw);
    if (isset($raw['hero']) || isset($raw['intro']) || isset($raw['assistant'])) {
        $m['home']['meta'] = array_replace_recursive(
            $m['home']['meta'],
            is_array($raw['meta'] ?? null) ? $raw['meta'] : []
        );
        $m['home']['blocks'] = build_legacy_home_blocks($m);
    }
    if (!isset($raw['professionals']['blocks']) || !is_array($raw['professionals']['blocks'])) {
        $m['professionals'] = $def['professionals'];
    }

    if (empty($m['home']['blocks'])) {
        $m['home']['blocks'] = $def['home']['blocks'];
    }
    if (empty($m['professionals']['blocks'])) {
        $m['professionals']['blocks'] = $def['professionals']['blocks'];
    }
    if (empty($m['institutions']['blocks'])) {
        $m['institutions']['blocks'] = $def['institutions']['blocks'];
    }
    if (empty($m['blog']['blocks'])) {
        $m['blog']['blocks'] = $def['blog']['blocks'];
    }
    if (empty($m['partners']['blocks'])) {
        $m['partners']['blocks'] = $def['partners']['blocks'];
    }

    return $m;
}

function build_legacy_home_blocks(array $m): array
{
    $hero = is_array($m['hero'] ?? null) ? $m['hero'] : [];
    $intro = is_array($m['intro'] ?? null) ? $m['intro'] : [];
    $assistant = is_array($m['assistant'] ?? null) ? $m['assistant'] : [];
    $closing = is_array($m['closing'] ?? null) ? $m['closing'] : [];

    $panels = [[
        'title' => (string) ($assistant['title'] ?? ''),
        'lead' => (string) ($assistant['lead'] ?? ''),
        'body' => (string) ($assistant['paragraph2'] ?? ''),
        'emphasis' => (string) ($assistant['paragraph3'] ?? ''),
        'sidebar' => (string) ($assistant['sidebar_title'] ?? ''),
        'features' => is_array($assistant['features'] ?? null) ? $assistant['features'] : [],
        'bottom_title' => (string) ($assistant['bottom_title'] ?? ''),
        'bottom_link' => is_array($assistant['bottom_link'] ?? null) ? $assistant['bottom_link'] : ['label' => '', 'href' => '#'],
    ]];

    return [
        ['type' => 'hero_fullscreen', 'id' => 'legacy-hero', 'props' => $hero],
        [
            'type' => 'intro_gradient',
            'id' => 'legacy-intro',
            'props' => [
                'headline_line1' => (string) ($intro['headline_line1'] ?? ''),
                'headline_line2' => (string) ($intro['headline_line2'] ?? ''),
                'body' => (string) ($intro['body'] ?? $intro['tagline'] ?? ''),
            ],
        ],
        [
            'type' => 'product_tabs',
            'id' => 'legacy-product',
            'props' => [
                'tabs' => is_array($assistant['tabs'] ?? null) ? $assistant['tabs'] : ['Product'],
                'active_tab' => (int) ($assistant['active_tab'] ?? 0),
                'panels' => $panels,
            ],
        ],
        ['type' => 'closing_block', 'id' => 'legacy-closing', 'props' => $closing],
    ];
}

function default_site(): array
{
    return [
        'seo' => xr_site_seo_defaults(),
        'hubspot' => xr_default_hubspot(),
        'nav' => xr_default_nav(),
        'home' => [
            'meta' => array_replace_recursive(xr_page_meta_schema(), [
                'title' => 'XR Doctor Platform',
                'description' => 'AI-powered XR platform for medical professionals and institutions — holographic imaging, training, and collaboration in AR/VR.',
                'keywords' => 'medical XR, AR healthcare, VR surgery, holographic imaging, clinical collaboration, medical training',
                'og_type' => 'website',
            ]),
            'blocks' => xr_default_home_blocks(),
        ],
        'professionals' => [
            'meta' => array_replace_recursive(xr_page_meta_schema(), [
                'title' => 'XR Doctor for Professionals',
                'description' => 'Clinical XR tools for diagnostics, education, and team collaboration — deploy holographic workflows tailored to your practice.',
                'keywords' => 'clinical XR, surgeon tools, medical AR, telementoring, holographic rounds',
            ]),
            'blocks' => xr_default_professionals_blocks(),
        ],
        'institutions' => [
            'meta' => array_replace_recursive(xr_page_meta_schema(), [
                'title' => 'XR Doctor for Institutions',
                'description' => 'Hospital and university programs: scalable XR deployment, governance, training, and research-ready collaboration.',
                'keywords' => 'hospital XR, medical university AR, enterprise healthcare VR, institutional rollout',
            ]),
            'blocks' => xr_default_institutions_blocks(),
        ],
        'blog' => [
            'meta' => array_replace_recursive(xr_page_meta_schema(), [
                'title' => 'XR Doctor Blog',
                'description' => 'Clinical notes, product updates, research digests, and stories from teams scaling holographic care.',
                'keywords' => 'medical XR news, healthcare AR updates, clinical innovation',
                'og_type' => 'website',
            ]),
            'blocks' => xr_default_blog_blocks(),
        ],
        'partners' => [
            'meta' => array_replace_recursive(xr_page_meta_schema(), [
                'title' => 'Partner with XR Doctor',
                'description' => 'Technology, distribution, and clinical partnership tracks — co-build the ecosystem for scalable medical XR.',
                'keywords' => 'healthcare partnerships, medical XR OEM, clinical distribution, medtech alliances',
            ]),
            'blocks' => xr_default_partners_blocks(),
        ],
    ];
}

function save_site(array $data): bool
{
    if (xr_site_uses_database() && is_readable(xr_site_db_path())) {
        $ok = xr_db_persist_site(xr_db(), $data);
        if ($ok) {
            xr_site_sync_json_backup($data);
        }

        return $ok;
    }

    $dir = dirname(site_data_path());
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $payload = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if ($payload === false) {
        return false;
    }

    return file_put_contents(site_data_path(), $payload, LOCK_EX) !== false;
}

/**
 * Подготовка меню: href + активный пункт по текущей странице.
 *
 * @param 'home'|'professionals'|'institutions'|'blog'|'partners' $pageId
 */
function xr_resolved_nav(array $site, string $pageId): array
{
    $nav = $site['nav'];
    $items = [];
    foreach ($nav['items'] as $it) {
        if (!is_array($it)) {
            continue;
        }
        $p = (string) ($it['page'] ?? '');
        $active = $p !== '' && $p === $pageId;
        $row = $it;
        $row['active'] = $active;
        $items[] = $row;
    }
    return [
        'logo_alt' => (string) ($nav['logo_alt'] ?? ''),
        'items' => $items,
        'cta_outline' => is_array($nav['cta_outline'] ?? null) ? $nav['cta_outline'] : ['label' => '', 'href' => '#'],
        'cta_gradient' => is_array($nav['cta_gradient'] ?? null) ? $nav['cta_gradient'] : ['label' => '', 'href' => '#'],
    ];
}

function h(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function admin_config(): array
{
    $defaults = ['password_hash' => ''];
    if (!is_readable(CONFIG_PATH)) {
        return $defaults;
    }
    $cfg = require CONFIG_PATH;
    return is_array($cfg) ? array_merge($defaults, $cfg) : $defaults;
}

function admin_logged_in(): bool
{
    return !empty($_SESSION['admin_ok']);
}

function require_admin(): void
{
    if (!admin_logged_in()) {
        header('Location: /admin/login.php', true, 302);
        exit;
    }
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_validate(?string $token): bool
{
    return is_string($token)
        && isset($_SESSION['csrf'])
        && hash_equals($_SESSION['csrf'], $token);
}
