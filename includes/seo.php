<?php

declare(strict_types=1);

/**
 * Site-wide SEO defaults (English-first). Editable in Admin → SEO.
 */
function xr_site_seo_defaults(): array
{
    return [
        'canonical_origin' => '',
        'site_name' => 'XR Doctor',
        'language' => 'en',
        'locale' => 'en_US',
        'title_separator' => ' · ',
        'append_site_name' => true,
        'twitter_site' => '',
        'twitter_creator' => '',
        'default_og_image' => '/assets/img/logo.png',
        'facebook_app_id' => '',
        'organization_name' => 'XR Doctor',
        'organization_url' => '',
        'organization_logo_url' => '/assets/img/logo.png',
    ];
}

/**
 * Per-page meta: document title & description plus Open Graph / Twitter / robots.
 */
function xr_page_meta_schema(): array
{
    return [
        'title' => '',
        'description' => '',
        'keywords' => '',
        'robots' => '',
        'canonical_path' => '',
        'og_title' => '',
        'og_description' => '',
        'og_image' => '',
        'og_type' => 'website',
        'twitter_card' => 'summary_large_image',
    ];
}

/**
 * @param array<string, mixed> $meta
 * @return array<string, string>
 */
function xr_normalize_page_meta(array $meta): array
{
    $def = xr_page_meta_schema();
    $out = [];
    foreach ($def as $k => $_) {
        $v = $meta[$k] ?? '';
        $out[$k] = is_string($v) ? trim($v) : '';
    }
    if ($out['og_type'] === '') {
        $out['og_type'] = 'website';
    }
    if (!in_array($out['og_type'], ['website', 'article'], true)) {
        $out['og_type'] = 'website';
    }
    if ($out['twitter_card'] === '') {
        $out['twitter_card'] = 'summary_large_image';
    }
    if (!in_array($out['twitter_card'], ['summary', 'summary_large_image', 'app', 'player'], true)) {
        $out['twitter_card'] = 'summary_large_image';
    }

    return $out;
}

/**
 * @param array<string, mixed> $seo
 * @return array<string, string|bool>
 */
function xr_normalize_site_seo(array $seo): array
{
    $def = xr_site_seo_defaults();
    $out = [];
    foreach ($def as $k => $default) {
        $v = $seo[$k] ?? $default;
        if ($k === 'append_site_name') {
            $out[$k] = filter_var($v, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
            if ($out[$k] === null) {
                $out[$k] = (bool) $default;
            }

            continue;
        }
        $out[$k] = is_string($v) ? trim($v) : (string) $default;
    }

    return $out;
}

/**
 * @param array<string, mixed> $site
 */
function xr_normalize_site_for_seo(array $site): array
{
    if (!isset($site['seo']) || !is_array($site['seo'])) {
        $site['seo'] = [];
    }
    $site['seo'] = xr_normalize_site_seo(array_merge(xr_site_seo_defaults(), $site['seo']));

    foreach (XR_PAGE_SLUGS as $slug) {
        if (!isset($site[$slug]['meta']) || !is_array($site[$slug]['meta'])) {
            $site[$slug]['meta'] = [];
        }
        $site[$slug]['meta'] = xr_normalize_page_meta($site[$slug]['meta']);
    }

    return $site;
}

function xr_seo_page_path(string $pageId): string
{
    $map = [
        'home' => '/',
        'professionals' => '/professionals.php',
        'institutions' => '/institutions.php',
        'blog' => '/blog.php',
        'partners' => '/partners.php',
    ];

    return $map[$pageId] ?? '/';
}

function xr_seo_resolve_origin(array $seo): string
{
    $o = trim((string) ($seo['canonical_origin'] ?? ''));
    if ($o !== '') {
        return rtrim(preg_replace('#/+$#', '', $o) ?? '', '/');
    }
    $host = (string) ($_SERVER['HTTP_HOST'] ?? '');
    if ($host === '') {
        return '';
    }
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || ((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
        || ((int) ($_SERVER['SERVER_PORT'] ?? 0) === 443);

    return ($https ? 'https' : 'http') . '://' . $host;
}

function xr_seo_absolute_url(string $origin, string $pathOrUrl): string
{
    $pathOrUrl = trim($pathOrUrl);
    if ($pathOrUrl === '') {
        return '';
    }
    if (preg_match('#^https?://#i', $pathOrUrl)) {
        return $pathOrUrl;
    }
    if ($origin === '') {
        return $pathOrUrl;
    }

    return $origin . '/' . ltrim($pathOrUrl, '/');
}

function xr_seo_title_contains_brand(string $title, string $brand): bool
{
    if ($brand === '' || $title === '') {
        return false;
    }

    return stripos($title, $brand) !== false;
}

/**
 * @param array<string, mixed> $site
 * @param 'home'|'professionals'|'institutions'|'blog'|'partners' $pageId
 * @return array{
 *   html_title: string,
 *   description: string,
 *   keywords: string,
 *   robots: string,
 *   canonical_url: string,
 *   og_title: string,
 *   og_description: string,
 *   og_image: string,
 *   og_type: string,
 *   twitter_card: string,
 *   locale: string,
 *   language: string,
 *   site_name: string,
 *   twitter_site: string,
 *   twitter_creator: string,
 *   facebook_app_id: string,
 *   json_ld: string
 * }
 */
function xr_build_seo_context(array $site, string $pageId): array
{
    $seo = xr_normalize_site_seo(is_array($site['seo'] ?? null) ? $site['seo'] : []);
    $meta = xr_normalize_page_meta(is_array($site[$pageId]['meta'] ?? null) ? $site[$pageId]['meta'] : []);

    $origin = xr_seo_resolve_origin($seo);
    $path = $meta['canonical_path'] !== '' ? $meta['canonical_path'] : xr_seo_page_path($pageId);
    if ($path !== '' && $path[0] !== '/') {
        $path = '/' . $path;
    }
    $canonicalUrl = $origin !== '' ? xr_seo_absolute_url($origin, $path) : $path;

    $baseTitle = $meta['title'];
    $siteName = (string) ($seo['site_name'] ?? '');
    $append = !empty($seo['append_site_name']);
    if ($baseTitle === '' && $siteName !== '') {
        $htmlTitle = $siteName;
    } elseif ($append && $siteName !== '' && $baseTitle !== '' && !xr_seo_title_contains_brand($baseTitle, $siteName)) {
        $sep = (string) ($seo['title_separator'] ?? ' · ');
        $htmlTitle = $baseTitle . $sep . $siteName;
    } else {
        $htmlTitle = $baseTitle;
    }

    $description = $meta['description'];
    $ogTitle = $meta['og_title'] !== '' ? $meta['og_title'] : ($baseTitle !== '' ? $baseTitle : $htmlTitle);
    $ogDescription = $meta['og_description'] !== '' ? $meta['og_description'] : $description;
    $ogImagePath = $meta['og_image'] !== '' ? $meta['og_image'] : (string) ($seo['default_og_image'] ?? '');
    $ogImage = xr_seo_absolute_url($origin, $ogImagePath);

    $robots = $meta['robots'];
    if ($robots === '') {
        $robots = 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';
    }

    $jsonLd = xr_seo_json_ld($seo, $meta, $canonicalUrl, $ogTitle, $ogDescription, $ogImage);

    return [
        'html_title' => $htmlTitle,
        'description' => $description,
        'keywords' => $meta['keywords'],
        'robots' => $robots,
        'canonical_url' => $canonicalUrl,
        'og_title' => $ogTitle,
        'og_description' => $ogDescription,
        'og_image' => $ogImage,
        'og_type' => $meta['og_type'],
        'twitter_card' => $meta['twitter_card'],
        'locale' => (string) ($seo['locale'] ?? 'en_US'),
        'language' => (string) ($seo['language'] ?? 'en'),
        'site_name' => $siteName,
        'twitter_site' => ltrim(trim((string) ($seo['twitter_site'] ?? '')), '@'),
        'twitter_creator' => ltrim(trim((string) ($seo['twitter_creator'] ?? '')), '@'),
        'facebook_app_id' => trim((string) ($seo['facebook_app_id'] ?? '')),
        'json_ld' => $jsonLd,
    ];
}

/**
 * @param array<string, string|bool> $seo
 * @param array<string, string> $meta
 */
function xr_seo_json_ld(array $seo, array $meta, string $pageUrl, string $pageName, string $description, string $imageUrl): string
{
    $orgName = trim((string) ($seo['organization_name'] ?? ''));
    $orgUrl = trim((string) ($seo['organization_url'] ?? ''));
    $orgLogo = trim((string) ($seo['organization_logo_url'] ?? ''));
    $origin = xr_seo_resolve_origin($seo);

    $graph = [];

    $org = [
        '@type' => 'Organization',
        'name' => $orgName !== '' ? $orgName : (string) ($seo['site_name'] ?? 'Organization'),
    ];
    if ($orgUrl !== '') {
        $org['url'] = xr_seo_absolute_url($origin, $orgUrl);
    }
    if ($orgLogo !== '') {
        $org['logo'] = xr_seo_absolute_url($origin, $orgLogo);
    }
    $graph[] = $org;

    $siteName = (string) ($seo['site_name'] ?? '');
    $website = [
        '@type' => 'WebSite',
        '@id' => $origin !== '' ? $origin . '/#website' : '/#website',
        'name' => $siteName !== '' ? $siteName : $org['name'],
        'publisher' => ['@type' => 'Organization', 'name' => $org['name']],
    ];
    if ($origin !== '') {
        $website['url'] = $origin . '/';
    }
    $graph[] = $website;

    $webPage = [
        '@type' => 'WebPage',
        'name' => $pageName,
        'isPartOf' => ['@id' => $website['@id']],
    ];
    if ($description !== '') {
        $webPage['description'] = $description;
    }
    if ($pageUrl !== '') {
        $webPage['url'] = $pageUrl;
    }
    if ($imageUrl !== '') {
        $webPage['primaryImageOfPage'] = ['@type' => 'ImageObject', 'url' => $imageUrl];
    }
    $graph[] = $webPage;

    $payload = [
        '@context' => 'https://schema.org',
        '@graph' => $graph,
    ];
    $enc = json_encode(
        $payload,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS
    );
    if ($enc === false) {
        return '';
    }

    return $enc;
}

/**
 * @param array<string, mixed> $site
 * @param 'home'|'professionals'|'institutions'|'blog'|'partners' $pageId
 */
function xr_render_seo_head(array $site, string $pageId): void
{
    if (!in_array($pageId, XR_PAGE_SLUGS, true)) {
        $pageId = 'home';
    }
    $ctx = xr_build_seo_context($site, $pageId);
    $lang = $ctx['language'] !== '' ? $ctx['language'] : 'en';

    echo '<meta charset="UTF-8">' . "\n";
    echo '    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">' . "\n";
    echo '    <meta name="theme-color" content="#212529">' . "\n";
    echo '    <meta name="color-scheme" content="dark">' . "\n";
    echo '    <title>' . h($ctx['html_title']) . "</title>\n";

    if ($ctx['description'] !== '') {
        echo '    <meta name="description" content="' . h($ctx['description']) . "\">\n";
    }
    if ($ctx['keywords'] !== '') {
        echo '    <meta name="keywords" content="' . h($ctx['keywords']) . "\">\n";
    }
    echo '    <meta name="robots" content="' . h($ctx['robots']) . "\">\n";
    echo '    <meta name="author" content="' . h($ctx['site_name']) . "\">\n";
    echo '    <link rel="canonical" href="' . h($ctx['canonical_url']) . "\">\n";
    echo '    <link rel="alternate" hreflang="' . h($lang) . '" href="' . h($ctx['canonical_url']) . "\">\n";

    echo '    <meta property="og:locale" content="' . h($ctx['locale']) . "\">\n";
    echo '    <meta property="og:type" content="' . h($ctx['og_type']) . "\">\n";
    echo '    <meta property="og:title" content="' . h($ctx['og_title']) . "\">\n";
    if ($ctx['og_description'] !== '') {
        echo '    <meta property="og:description" content="' . h($ctx['og_description']) . "\">\n";
    }
    echo '    <meta property="og:url" content="' . h($ctx['canonical_url']) . "\">\n";
    if ($ctx['site_name'] !== '') {
        echo '    <meta property="og:site_name" content="' . h($ctx['site_name']) . "\">\n";
    }
    if ($ctx['og_image'] !== '') {
        echo '    <meta property="og:image" content="' . h($ctx['og_image']) . "\">\n";
        echo '    <meta property="og:image:alt" content="' . h($ctx['og_title']) . "\">\n";
    }
    if ($ctx['facebook_app_id'] !== '') {
        echo '    <meta property="fb:app_id" content="' . h($ctx['facebook_app_id']) . "\">\n";
    }

    echo '    <meta name="twitter:card" content="' . h($ctx['twitter_card']) . "\">\n";
    echo '    <meta name="twitter:title" content="' . h($ctx['og_title']) . "\">\n";
    if ($ctx['og_description'] !== '') {
        echo '    <meta name="twitter:description" content="' . h($ctx['og_description']) . "\">\n";
    }
    if ($ctx['og_image'] !== '') {
        echo '    <meta name="twitter:image" content="' . h($ctx['og_image']) . "\">\n";
    }
    if ($ctx['twitter_site'] !== '') {
        echo '    <meta name="twitter:site" content="@' . h($ctx['twitter_site']) . "\">\n";
    }
    if ($ctx['twitter_creator'] !== '') {
        echo '    <meta name="twitter:creator" content="@' . h($ctx['twitter_creator']) . "\">\n";
    }

    if ($ctx['json_ld'] !== '') {
        echo '    <script type="application/ld+json">' . "\n" . $ctx['json_ld'] . "\n    </script>\n";
    }
}

/**
 * @param array<string, mixed>|null $post
 * @param array<string, string|bool> $currentSeo
 * @return array<string, string|bool>
 */
function xr_seo_merge_from_post(?array $post, array $currentSeo): array
{
    $base = array_replace_recursive(xr_site_seo_defaults(), $currentSeo);
    if (!is_array($post)) {
        return xr_normalize_site_seo($base);
    }
    foreach (array_keys(xr_site_seo_defaults()) as $k) {
        if ($k === 'append_site_name') {
            continue;
        }
        if (array_key_exists($k, $post)) {
            $base[$k] = trim((string) $post[$k]);
        }
    }
    if (array_key_exists('append_site_name', $post)) {
        $v = $post['append_site_name'];
        $base['append_site_name'] = $v === '1' || $v === 'on' || $v === true || $v === 1;
    }

    return xr_normalize_site_seo($base);
}

/**
 * @param array<string, mixed>|null $post
 * @param array<string, string> $currentMeta
 * @return array<string, string>
 */
function xr_page_meta_merge_from_post(?array $post, array $currentMeta): array
{
    $base = xr_normalize_page_meta($currentMeta);
    if (!is_array($post)) {
        return $base;
    }
    foreach (array_keys(xr_page_meta_schema()) as $k) {
        if (array_key_exists($k, $post)) {
            $base[$k] = trim((string) $post[$k]);
        }
    }

    return xr_normalize_page_meta($base);
}
