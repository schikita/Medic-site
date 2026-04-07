<?php

declare(strict_types=1);

function xr_find_block_props(array $blocks, string $type): array
{
    foreach ($blocks as $b) {
        if (!is_array($b)) {
            continue;
        }
        if (($b['type'] ?? '') === $type) {
            $p = $b['props'] ?? [];

            return is_array($p) ? $p : [];
        }
    }

    return [];
}

/**
 * Вкладки блока «очки» + YouTube (home, id block-1-4-5) — постер и ID ролика для каждой кнопки.
 *
 * @return array{tabs: list<array{label: string, poster: string, youtube_id: string}>}
 */
function xr_oculus_tabs_home_state(array $site): array
{
    $blocks = $site['home']['blocks'] ?? [];
    $defaults = [
        ['label' => '', 'poster' => '', 'youtube_id' => ''],
        ['label' => '', 'poster' => '', 'youtube_id' => ''],
        ['label' => '', 'poster' => '', 'youtube_id' => ''],
    ];
    foreach ($blocks as $b) {
        if (!is_array($b) || ($b['id'] ?? '') !== 'block-1-4-5' || ($b['type'] ?? '') !== 'tabs_youtube_loop') {
            continue;
        }
        $props = is_array($b['props'] ?? null) ? $b['props'] : [];
        $tabs = is_array($props['tabs'] ?? null) ? $props['tabs'] : [];
        $out = [];
        for ($i = 0; $i < 3; $i++) {
            $t = is_array($tabs[$i] ?? null) ? $tabs[$i] : [];
            $out[] = [
                'label' => (string) ($t['label'] ?? ''),
                'poster' => (string) ($t['poster'] ?? ''),
                'youtube_id' => (string) ($t['youtube_id'] ?? ''),
            ];
        }

        return ['tabs' => $out];
    }

    return ['tabs' => $defaults];
}

function xr_clinical_circles_state(array $site): array
{
    $props = xr_find_block_props($site['home']['blocks'] ?? [], 'clinical_circles');
    $circles = is_array($props['circles'] ?? null) ? $props['circles'] : [];
    $out = [];
    for ($i = 0; $i < 3; $i++) {
        $c = is_array($circles[$i] ?? null) ? $circles[$i] : [];
        $out[] = [
            'src' => (string) ($c['src'] ?? ''),
            'alt' => (string) ($c['alt'] ?? ''),
        ];
    }

    return [
        'label'   => (string) ($props['label']   ?? ''),
        'heading' => (string) ($props['heading']  ?? ''),
        'subhead' => (string) ($props['subhead']  ?? ''),
        'tagline' => (string) ($props['tagline']  ?? ''),
        'circles' => $out,
    ];
}

function xr_saves_your_state(array $site): array
{
    $props = xr_find_block_props($site['home']['blocks'] ?? [], 'saves_your');
    $items = is_array($props['items'] ?? null) ? $props['items'] : [];
    $images = [];
    for ($i = 0; $i < 3; $i++) {
        $images[$i] = (string) ($items[$i]['image'] ?? '');
    }
    return ['images' => $images];
}

function xr_gallery_three_state(array $site): array
{
    $props   = xr_find_block_props($site['home']['blocks'] ?? [], 'requirements_grid');
    $columns = is_array($props['columns'] ?? null) ? $props['columns'] : [];

    $icons = [];
    foreach ($columns as $ci => $col) {
        $groups = is_array($col['groups'] ?? null) ? $col['groups'] : [];
        foreach ($groups as $gi => $g) {
            $icons[$ci . '_' . $gi] = (string) ($g['icon'] ?? '');
        }
    }

    // AR/VR Glasses list item icons (column 2, group 0)
    $glassesCol = $columns[2] ?? [];
    $glassesGroups = is_array($glassesCol['groups'] ?? null) ? $glassesCol['groups'] : [];
    $glassesItems = is_array(($glassesGroups[0]['items'] ?? null)) ? $glassesGroups[0]['items'] : [];
    $itemIcons = [];
    foreach ($glassesItems as $ii => $item) {
        $itemIcons[$ii] = is_array($item) ? (string) ($item['icon'] ?? '') : '';
    }

    return [
        'device_image' => (string) ($props['device_image'] ?? ''),
        'icons'        => $icons,
        'item_icons'   => $itemIcons,
    ];
}

function xr_team_visioners_state(array $site): array
{
    $props  = xr_find_block_props($site['home']['blocks'] ?? [], 'team_visioners');
    $photos = is_array($props['photos'] ?? null) ? $props['photos'] : [];
    $out    = [];
    for ($i = 0; $i < 2; $i++) {
        $ph     = is_array($photos[$i] ?? null) ? $photos[$i] : [];
        $out[]  = [
            'src' => (string) ($ph['src'] ?? ''),
            'alt' => (string) ($ph['alt'] ?? ''),
        ];
    }

    return [
        'photos' => $out,
        'image'  => (string) ($props['image'] ?? ''),
    ];
}

function xr_product_tabs_state(array $site): array
{
    $props = xr_find_block_props($site['home']['blocks'] ?? [], 'product_tabs');
    $panels = is_array($props['panels'] ?? null) ? $props['panels'] : [];
    $first = is_array($panels[0] ?? null) ? $panels[0] : [];
    $panel2 = is_array($panels[2] ?? null) ? $panels[2] : [];
    $fg = is_array($panel2['feature_grid'] ?? null) ? $panel2['feature_grid'] : [];
    $gridIconImgs = [];
    for ($i = 0; $i < 4; $i++) {
        $fi = is_array($fg[$i] ?? null) ? $fg[$i] : [];
        $gridIconImgs[] = (string) ($fi['icon_img'] ?? '');
    }

    return [
        'tabs' => is_array($props['tabs'] ?? null) ? $props['tabs'] : [],
        'active_tab' => (int) ($props['active_tab'] ?? 0),
        'video_youtube_id' => (string) ($first['youtube_id'] ?? ''),
        'video_poster'     => (string) ($first['poster'] ?? ''),
        'video_label'      => (string) ($first['video_label'] ?? ''),
        'card_image_0'     => (string) (($panels[0]['card_image'] ?? $panels[0]['poster'] ?? '')),
        'card_image_1'     => (string) (($panels[1]['card_image'] ?? $panels[1]['poster'] ?? '')),
        'grid_icon_imgs'   => $gridIconImgs,
        'title' => (string) ($first['title'] ?? ''),
        'lead' => (string) ($first['lead'] ?? ''),
        'paragraph2' => (string) ($first['body'] ?? ''),
        'paragraph3' => (string) ($first['emphasis'] ?? ''),
        'sidebar_title' => (string) ($first['sidebar'] ?? ''),
        'features' => is_array($first['features'] ?? null) ? $first['features'] : [],
        'bottom_title' => (string) ($first['bottom_title'] ?? ''),
        'bottom_link' => is_array($first['bottom_link'] ?? null) ? $first['bottom_link'] : ['label' => '', 'href' => '#'],
    ];
}

/* ── helpers for non-home pages ──────────────────────────────────── */

function xr_find_block_props_by_id(array $blocks, string $id): array
{
    foreach ($blocks as $b) {
        if (is_array($b) && ($b['id'] ?? '') === $id) {
            $p = $b['props'] ?? [];
            return is_array($p) ? $p : [];
        }
    }
    return [];
}

function xr_slides_state(array $props, int $count = 3): array
{
    $slides = is_array($props['slides'] ?? null) ? $props['slides'] : [];
    $out = [];
    for ($i = 0; $i < $count; $i++) {
        $s = is_array($slides[$i] ?? null) ? $slides[$i] : [];
        $out[] = ['image' => (string)($s['image'] ?? ''), 'title' => (string)($s['title'] ?? '')];
    }
    return $out;
}

function xr_professionals_state(array $site): array
{
    $blocks  = $site['professionals']['blocks'] ?? [];
    $hero    = xr_find_block_props_by_id($blocks, 'p-2-intro');
    $engage  = xr_find_block_props_by_id($blocks, 'p-2-2');
    $youtube = xr_find_block_props_by_id($blocks, 'p-2-5');
    $gallery = xr_find_block_props_by_id($blocks, 'p-2-9');
    return [
        'hero_image'    => (string)($hero['image'] ?? ''),
        'hero_title'    => (string)($hero['title'] ?? ''),
        'hero_subtitle' => (string)($hero['subtitle'] ?? ''),
        'engage_eyebrow'   => (string)($engage['eyebrow'] ?? ''),
        'engage_title'     => (string)($engage['title'] ?? ''),
        'engage_taglines'  => implode("\n", is_array($engage['taglines'] ?? null) ? $engage['taglines'] : []),
        'engage_card_title' => (string)($engage['card_title'] ?? ''),
        'engage_card_icon'  => (string)($engage['card_icon'] ?? ''),
        'engage_card_yt'    => (string)($engage['card_youtube_id'] ?? ''),
        'engage_card_mp4'   => (string)($engage['card_mp4'] ?? ''),
        'engage_card_poster'=> (string)($engage['card_poster'] ?? ''),
        'yt_heading'    => (string)($youtube['heading'] ?? ''),
        'yt_id'         => (string)($youtube['youtube_id'] ?? ''),
        'gallery_heading' => (string)($gallery['heading'] ?? ''),
        'gallery_slides'  => xr_slides_state($gallery),
    ];
}

function xr_institutions_state(array $site): array
{
    $blocks  = $site['institutions']['blocks'] ?? [];
    $carousel = xr_find_block_props_by_id($blocks, 'i-3-1');
    $gallery  = xr_find_block_props_by_id($blocks, 'i-3-21-24');
    return [
        'carousel_heading' => (string)($carousel['heading'] ?? ''),
        'carousel_slides'  => xr_slides_state($carousel),
        'gallery_heading'  => (string)($gallery['heading'] ?? ''),
        'gallery_slides'   => xr_slides_state($gallery),
    ];
}

function xr_blog_state(array $site): array
{
    $blocks = $site['blog']['blocks'] ?? [];
    $hero   = xr_find_block_props_by_id($blocks, 'block-4-1');
    $grid   = xr_find_block_props_by_id($blocks, 'block-4-2-grid');
    $rawPosts = is_array($grid['posts'] ?? null) ? $grid['posts'] : [];
    $posts = [];
    for ($i = 0; $i < 5; $i++) {
        $p = is_array($rawPosts[$i] ?? null) ? $rawPosts[$i] : [];
        $posts[] = [
            'title'   => (string)($p['title'] ?? ''),
            'image'   => (string)($p['image'] ?? ''),
            'excerpt' => (string)($p['excerpt'] ?? ''),
        ];
    }
    return [
        'hero_image'    => (string)($hero['image'] ?? ''),
        'hero_title'    => (string)($hero['title'] ?? ''),
        'hero_subtitle' => (string)($hero['subtitle'] ?? ''),
        'posts'         => $posts,
    ];
}

function xr_partners_state(array $site): array
{
    $blocks = $site['partners']['blocks'] ?? [];
    $hero   = xr_find_block_props_by_id($blocks, 'block-5-1');
    $sv1    = xr_find_block_props_by_id($blocks, 'block-5-4');
    $sv2    = xr_find_block_props_by_id($blocks, 'block-5-8');
    $icons  = xr_find_block_props_by_id($blocks, 'block-5-5');
    $rawItems = is_array($icons['items'] ?? null) ? $icons['items'] : [];
    $iconItems = [];
    for ($i = 0; $i < 4; $i++) {
        $it = is_array($rawItems[$i] ?? null) ? $rawItems[$i] : [];
        $iconItems[] = [
            'label'    => (string)($it['label'] ?? ''),
            'dashicon' => (string)($it['dashicon'] ?? ''),
            'text'     => (string)($it['text'] ?? ''),
        ];
    }
    return [
        'hero_image'    => (string)($hero['image'] ?? ''),
        'hero_title'    => (string)($hero['title'] ?? ''),
        'hero_subtitle' => (string)($hero['subtitle'] ?? ''),
        'sv1_title'  => (string)($sv1['title'] ?? ''),
        'sv1_body'   => (string)($sv1['body'] ?? ''),
        'sv1_poster' => (string)($sv1['poster'] ?? ''),
        'sv1_mp4'    => (string)($sv1['mp4'] ?? ''),
        'sv2_title'  => (string)($sv2['title'] ?? ''),
        'sv2_body'   => (string)($sv2['body'] ?? ''),
        'sv2_poster' => (string)($sv2['poster'] ?? ''),
        'sv2_mp4'    => (string)($sv2['mp4'] ?? ''),
        'icons_title' => (string)($icons['title'] ?? ''),
        'icon_items'  => $iconItems,
    ];
}
