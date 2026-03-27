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

function xr_product_tabs_state(array $site): array
{
    $props = xr_find_block_props($site['home']['blocks'] ?? [], 'product_tabs');
    $panels = is_array($props['panels'] ?? null) ? $props['panels'] : [];
    $first = is_array($panels[0] ?? null) ? $panels[0] : [];

    return [
        'tabs' => is_array($props['tabs'] ?? null) ? $props['tabs'] : [],
        'active_tab' => (int) ($props['active_tab'] ?? 0),
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
