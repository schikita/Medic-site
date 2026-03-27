<?php

declare(strict_types=1);

function merge_site_from_post(array $current): array
{
    $out = $current;
    if (!is_array($out['blog'] ?? null)) {
        $out['blog'] = default_site()['blog'];
    }
    if (!is_array($out['partners'] ?? null)) {
        $out['partners'] = default_site()['partners'];
    }
    if (!is_array($out['institutions'] ?? null)) {
        $out['institutions'] = default_site()['institutions'];
    }
    if (!is_array($out['seo'] ?? null)) {
        $out['seo'] = xr_site_seo_defaults();
    }
    $defNav = default_site()['nav']['items'];

    $out['hubspot']['whitepaper_url'] = trim((string) ($_POST['hubspot_whitepaper'] ?? ''));
    $out['hubspot']['demo_url'] = trim((string) ($_POST['hubspot_demo_url'] ?? ''));

    $out['seo'] = xr_seo_merge_from_post(
        isset($_POST['seo']) && is_array($_POST['seo']) ? $_POST['seo'] : null,
        is_array($out['seo'] ?? null) ? $out['seo'] : []
    );

    $pmRoot = $_POST['page_meta'] ?? null;
    if (is_array($pmRoot)) {
        foreach (XR_PAGE_SLUGS as $slug) {
            if (!isset($out[$slug]) || !is_array($out[$slug])) {
                continue;
            }
            $row = $pmRoot[$slug] ?? null;
            if (is_array($row)) {
                $cur = is_array($out[$slug]['meta'] ?? null) ? $out[$slug]['meta'] : [];
                $out[$slug]['meta'] = xr_page_meta_merge_from_post($row, $cur);
            }
        }
    }

    $out['nav']['logo_alt'] = trim((string) ($_POST['logo_alt'] ?? '')) ?: (string) ($out['nav']['logo_alt'] ?? '');

    $labels = $_POST['nav_label'] ?? [];
    $hrefs = $_POST['nav_href'] ?? [];
    $items = [];
    if (is_array($labels) && is_array($hrefs)) {
        $n = max(count($labels), count($hrefs));
        for ($i = 0; $i < $n; $i++) {
            $lab = trim((string) ($labels[$i] ?? ''));
            $href = trim((string) ($hrefs[$i] ?? ''));
            if ($lab === '' && $href === '') {
                continue;
            }
            $base = is_array($defNav[$i] ?? null) ? $defNav[$i] : [];
            unset($base['active']);
            $items[] = array_merge($base, [
                'label' => $lab ?: 'Link',
                'href' => $href ?: '#',
            ]);
        }
    }
    if ($items !== []) {
        $out['nav']['items'] = $items;
    }

    $out['nav']['cta_outline']['label'] = trim((string) ($_POST['cta_outline_label'] ?? ''))
        ?: (string) ($out['nav']['cta_outline']['label'] ?? '');
    $out['nav']['cta_outline']['href'] = trim((string) ($_POST['cta_outline_href'] ?? '#'));
    $out['nav']['cta_gradient']['label'] = trim((string) ($_POST['cta_gradient_label'] ?? ''))
        ?: (string) ($out['nav']['cta_gradient']['label'] ?? '');
    $out['nav']['cta_gradient']['href'] = trim((string) ($_POST['cta_gradient_href'] ?? '#'));

    $blocks = $out['home']['blocks'] ?? [];
    if (!is_array($blocks)) {
        $blocks = [];
    }

    foreach ($blocks as &$b) {
        if (!is_array($b)) {
            continue;
        }
        $type = (string) ($b['type'] ?? '');
        if (!isset($b['props']) || !is_array($b['props'])) {
            $b['props'] = [];
        }
        $p = &$b['props'];

        if ($type === 'hero_fullscreen') {
            $p['poster'] = trim((string) ($_POST['hero_poster'] ?? '')) ?: (string) ($p['poster'] ?? '');
            $p['video_mp4'] = trim((string) ($_POST['hero_video_mp4'] ?? ''));
            $p['video_webm'] = trim((string) ($_POST['hero_video_webm'] ?? ''));
            $p['overlay_note'] = trim((string) ($_POST['hero_overlay_note'] ?? ''));
            $lines = preg_split('/\r\n|\r|\n/', (string) ($_POST['hero_overlay_lines'] ?? '')) ?: [];
            $p['overlay_lines'] = array_values(array_filter(array_map('trim', $lines), static fn ($l) => $l !== ''));
        }

        if ($type === 'intro_gradient') {
            $p['headline_line1'] = trim((string) ($_POST['intro_headline_line1'] ?? '')) ?: (string) ($p['headline_line1'] ?? '');
            $p['headline_line2'] = trim((string) ($_POST['intro_headline_line2'] ?? '')) ?: (string) ($p['headline_line2'] ?? '');
            $p['tagline'] = trim((string) ($_POST['intro_tagline'] ?? ''));
        }

        if ($type === 'product_tabs') {
            $tabLines = preg_split('/\r\n|\r|\n/', (string) ($_POST['assistant_tabs'] ?? '')) ?: [];
            $tabs = array_values(array_filter(array_map('trim', $tabLines), static fn ($t) => $t !== ''));
            if ($tabs !== []) {
                $p['tabs'] = $tabs;
            }
            $at = (int) ($_POST['assistant_active_tab'] ?? 0);
            $p['active_tab'] = max(0, min($at, count($p['tabs'] ?? []) - 1));

            $panels = is_array($p['panels'] ?? null) ? $p['panels'] : [];
            if (!isset($panels[0]) || !is_array($panels[0])) {
                $panels[0] = [];
            }
            $panels[0]['title'] = trim((string) ($_POST['assistant_title'] ?? '')) ?: (string) ($panels[0]['title'] ?? '');
            $panels[0]['lead'] = trim((string) ($_POST['assistant_lead'] ?? ''));
            $panels[0]['body'] = trim((string) ($_POST['assistant_paragraph2'] ?? ''));
            $panels[0]['emphasis'] = trim((string) ($_POST['assistant_paragraph3'] ?? ''));
            $panels[0]['sidebar'] = trim((string) ($_POST['assistant_sidebar_title'] ?? ''));
            $panels[0]['bottom_title'] = trim((string) ($_POST['assistant_bottom_title'] ?? ''));
            if (!isset($panels[0]['bottom_link']) || !is_array($panels[0]['bottom_link'])) {
                $panels[0]['bottom_link'] = ['label' => '', 'href' => '#'];
            }
            $panels[0]['bottom_link']['label'] = trim((string) ($_POST['assistant_bottom_link_label'] ?? ''));
            $panels[0]['bottom_link']['href'] = trim((string) ($_POST['assistant_bottom_link_href'] ?? '#'));

            $flabels = $_POST['feature_label'] ?? [];
            $features = [];
            if (is_array($flabels)) {
                foreach ($flabels as $fl) {
                    $t = trim((string) $fl);
                    if ($t !== '') {
                        $features[] = ['label' => $t];
                    }
                }
            }
            if ($features !== []) {
                $panels[0]['features'] = $features;
            }
            $p['panels'] = $panels;
        }

        if ($type === 'closing_block') {
            $p['line1'] = trim((string) ($_POST['closing_line1'] ?? ''));
            $p['line2'] = trim((string) ($_POST['closing_line2'] ?? ''));
        }

        unset($p);
    }
    unset($b);

    $out['home']['blocks'] = $blocks;

    $rawJson = trim((string) ($_POST['home_blocks_json'] ?? ''));
    if ($rawJson !== '') {
        $decoded = json_decode($rawJson, true);
        if (is_array($decoded)) {
            $out['home']['blocks'] = $decoded;
        }
    }

    $rawJsonPro = trim((string) ($_POST['professionals_blocks_json'] ?? ''));
    if ($rawJsonPro !== '') {
        $decoded = json_decode($rawJsonPro, true);
        if (is_array($decoded)) {
            $out['professionals']['blocks'] = $decoded;
        }
    }

    $rawJsonInst = trim((string) ($_POST['institutions_blocks_json'] ?? ''));
    if ($rawJsonInst !== '') {
        $decoded = json_decode($rawJsonInst, true);
        if (is_array($decoded)) {
            $out['institutions']['blocks'] = $decoded;
        }
    }

    $rawJsonBlog = trim((string) ($_POST['blog_blocks_json'] ?? ''));
    if ($rawJsonBlog !== '') {
        $decoded = json_decode($rawJsonBlog, true);
        if (is_array($decoded)) {
            $out['blog']['blocks'] = $decoded;
        }
    }

    $rawJsonPartners = trim((string) ($_POST['partners_blocks_json'] ?? ''));
    if ($rawJsonPartners !== '') {
        $decoded = json_decode($rawJsonPartners, true);
        if (is_array($decoded)) {
            $out['partners']['blocks'] = $decoded;
        }
    }

    return $out;
}
