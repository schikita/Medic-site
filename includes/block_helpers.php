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
