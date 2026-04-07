<?php

declare(strict_types=1);

function xr_admin_save_page_blocks(array &$out, string $page, callable $fn): void
{
    $blocks = $out[$page]['blocks'] ?? [];
    if (!is_array($blocks)) $blocks = [];
    $fn($blocks);
    $out[$page]['blocks'] = $blocks;
}

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
            $ytRaw = trim((string) ($_POST['hero_youtube_id'] ?? ''));
            if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $ytRaw, $m)) {
                $p['youtube_id'] = $m[1];
            } elseif (preg_match('/^[a-zA-Z0-9_-]{11}$/', $ytRaw)) {
                $p['youtube_id'] = $ytRaw;
            } else {
                $p['youtube_id'] = '';
            }
            $p['poster'] = trim((string) ($_POST['hero_poster'] ?? '')) ?: (string) ($p['poster'] ?? '');
            $p['video_mp4'] = trim((string) ($_POST['hero_video_mp4'] ?? ''));
            $p['video_webm'] = trim((string) ($_POST['hero_video_webm'] ?? ''));
            $p['overlay_note'] = trim((string) ($_POST['hero_overlay_note'] ?? ''));
            $lines = preg_split('/\r\n|\r|\n/', (string) ($_POST['hero_overlay_lines'] ?? '')) ?: [];
            $p['overlay_lines'] = array_values(array_filter(array_map('trim', $lines), static fn ($l) => $l !== ''));
        }

        if ($type === 'intro_gradient') {
            $p['eyebrow'] = trim((string) ($_POST['intro_eyebrow'] ?? ''));
            $p['headline_line1'] = trim((string) ($_POST['intro_headline_line1'] ?? '')) ?: (string) ($p['headline_line1'] ?? '');
            $p['headline_line2'] = trim((string) ($_POST['intro_headline_line2'] ?? '')) ?: (string) ($p['headline_line2'] ?? '');
            $p['body'] = trim((string) ($_POST['intro_body'] ?? ''));
            unset($p['tagline']);
        }

        if ($type === 'tabs_youtube_loop' && (($b['id'] ?? '') === 'block-1-4-5')) {
            $tabs = is_array($p['tabs'] ?? null) ? $p['tabs'] : [];
            for ($ti = 0; $ti < 3; $ti++) {
                if (!isset($tabs[$ti]) || !is_array($tabs[$ti])) {
                    $tabs[$ti] = [];
                }
                $tabs[$ti]['poster'] = trim((string) ($_POST['oculus_tab_poster_' . $ti] ?? ''));
                $tabs[$ti]['youtube_id'] = trim((string) ($_POST['oculus_tab_youtube_' . $ti] ?? ''));
                $tabs[$ti]['mode'] = 'youtube_click';
                if (!isset($tabs[$ti]['play_label'])) {
                    $tabs[$ti]['play_label'] = '';
                }
            }
            $p['tabs'] = $tabs;
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
            $ytRawA = trim((string) ($_POST['assistant_video_youtube_id'] ?? ''));
            if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $ytRawA, $m)) {
                $panels[0]['youtube_id'] = $m[1];
            } elseif (preg_match('/^[a-zA-Z0-9_-]{11}$/', $ytRawA)) {
                $panels[0]['youtube_id'] = $ytRawA;
            } else {
                $panels[0]['youtube_id'] = '';
            }
            $panels[0]['poster']      = trim((string) ($_POST['assistant_video_poster'] ?? ''));
            $panels[0]['video_label'] = trim((string) ($_POST['assistant_video_label'] ?? ''));
            $panels[0]['card_image']  = trim((string) ($_POST['assistant_card_image_0'] ?? ''));

            if (!isset($panels[1]) || !is_array($panels[1])) {
                $panels[1] = [];
            }
            $panels[1]['card_image']  = trim((string) ($_POST['assistant_card_image_1'] ?? ''));

            // Grid icon images (panel2 / feature_grid)
            if (!isset($panels[2]) || !is_array($panels[2])) {
                $panels[2] = [];
            }
            $fg2 = is_array($panels[2]['feature_grid'] ?? null) ? $panels[2]['feature_grid'] : [];
            for ($gi = 0; $gi < 4; $gi++) {
                $iconImg = trim((string) ($_POST['assistant_grid_icon_img_' . $gi] ?? ''));
                if ($iconImg !== '') {
                    if (!isset($fg2[$gi]) || !is_array($fg2[$gi])) {
                        $fg2[$gi] = [];
                    }
                    $fg2[$gi]['icon_img'] = $iconImg;
                }
            }
            if ($fg2 !== []) {
                $panels[2]['feature_grid'] = array_values($fg2);
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

        if ($type === 'video_freeze_section') {
            $ytRaw = trim((string) ($_POST['vfreeze_youtube_id'] ?? ''));
            // Accept full URL or bare ID
            if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $ytRaw, $m)) {
                $p['youtube_id'] = $m[1];
            } elseif (preg_match('/^[a-zA-Z0-9_-]{11}$/', $ytRaw)) {
                $p['youtube_id'] = $ytRaw;
            } else {
                $p['youtube_id'] = '';
            }
            $p['mp4']          = trim((string) ($_POST['vfreeze_mp4']     ?? ''));
            $p['poster']       = trim((string) ($_POST['vfreeze_poster']  ?? ''));
            $p['heading']      = trim((string) ($_POST['vfreeze_heading'] ?? ''));
            $p['heading_line2']= trim((string) ($_POST['vfreeze_heading2']?? ''));
            $p['intro']        = trim((string) ($_POST['vfreeze_intro']   ?? ''));
            $p['caption']      = trim((string) ($_POST['vfreeze_caption'] ?? ''));
        }

        if ($type === 'saves_your' && ($b['id'] ?? '') === 'block-1-18') {
            $items = is_array($p['items'] ?? null) ? $p['items'] : [[], [], []];
            for ($si = 0; $si < 3; $si++) {
                if (!is_array($items[$si] ?? null)) {
                    $items[$si] = [];
                }
                $key = 'saves_img_' . $si;
                if (isset($_POST[$key])) {
                    $items[$si]['image'] = trim((string) $_POST[$key]);
                }
            }
            $p['items'] = $items;
        }

        if ($type === 'requirements_grid' && ($b['id'] ?? '') === 'block-1-16-17') {
            $p['device_image'] = trim((string) ($_POST['reqgrid_device_image'] ?? ''));
            $columns = is_array($p['columns'] ?? null) ? $p['columns'] : [];
            foreach ($columns as $ci => &$col) {
                $groups = is_array($col['groups'] ?? null) ? $col['groups'] : [];
                foreach ($groups as $gi => &$g) {
                    $key = 'reqgrid_icon_' . $ci . '_' . $gi;
                    if (isset($_POST[$key])) {
                        $g['icon'] = trim((string) $_POST[$key]);
                    }
                }
                unset($g);
                $col['groups'] = $groups;
            }
            unset($col);
            // AR/VR Glasses list item icons
            if (isset($columns[2]['groups'][0]['items'])) {
                foreach ($columns[2]['groups'][0]['items'] as $ii => &$item) {
                    $key = 'reqgrid_item_icon_' . $ii;
                    if (isset($_POST[$key])) {
                        if (!is_array($item)) {
                            $item = ['text' => (string) $item, 'icon' => ''];
                        }
                        $item['icon'] = trim((string) $_POST[$key]);
                    }
                }
                unset($item);
            }
            $p['columns'] = $columns;
        }

        if ($type === 'team_visioners') {
            $photos = is_array($p['photos'] ?? null) ? $p['photos'] : [[], []];
            for ($vi = 0; $vi < 2; $vi++) {
                if (!is_array($photos[$vi] ?? null)) {
                    $photos[$vi] = [];
                }
                $photos[$vi]['src'] = trim((string) ($_POST['visioners_photo_src_' . $vi] ?? ''));
                $photos[$vi]['alt'] = trim((string) ($_POST['visioners_photo_alt_' . $vi] ?? ''));
            }
            $p['photos'] = $photos;
            $p['image']  = trim((string) ($_POST['visioners_image'] ?? ''));
        }

        if ($type === 'clinical_circles') {
            $circles = is_array($p['circles'] ?? null) ? $p['circles'] : [[], [], []];
            for ($ci = 0; $ci < 3; $ci++) {
                if (!is_array($circles[$ci] ?? null)) {
                    $circles[$ci] = [];
                }
                $circles[$ci]['src'] = trim((string) ($_POST['clinical_circle_src_' . $ci] ?? ''));
                $circles[$ci]['alt'] = trim((string) ($_POST['clinical_circle_alt_' . $ci] ?? ''));
            }
            $p['circles'] = $circles;
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

    /* ── Professionals structured fields ─────────────────────────── */
    xr_admin_save_page_blocks($out, 'professionals', function (array &$blocks): void {
        foreach ($blocks as &$b) {
            if (!is_array($b)) continue;
            $id = (string)($b['id'] ?? '');
            if (!isset($b['props']) || !is_array($b['props'])) $b['props'] = [];
            $p = &$b['props'];

            if ($id === 'p-2-intro') {
                $img = trim((string)($_POST['pro_hero_image'] ?? ''));
                if ($img !== '') $p['image'] = $img;
                $t = trim((string)($_POST['pro_hero_title'] ?? ''));
                if ($t !== '') $p['title'] = $t;
                $s = trim((string)($_POST['pro_hero_subtitle'] ?? ''));
                if ($s !== '') $p['subtitle'] = $s;
            }
            if ($id === 'p-2-2') {
                $ey = trim((string)($_POST['pro_engage_eyebrow'] ?? ''));
                if ($ey !== '') $p['eyebrow'] = $ey;
                $t = trim((string)($_POST['pro_engage_title'] ?? ''));
                if ($t !== '') $p['title'] = $t;
                $lines = preg_split('/\r\n|\r|\n/', (string)($_POST['pro_engage_taglines'] ?? '')) ?: [];
                $tl = array_values(array_filter(array_map('trim', $lines)));
                if ($tl !== []) $p['taglines'] = $tl;
                $ct = trim((string)($_POST['pro_engage_card_title'] ?? ''));
                if ($ct !== '') $p['card_title'] = $ct;
                $ci = trim((string)($_POST['pro_engage_card_icon'] ?? ''));
                if ($ci !== '') $p['card_icon'] = $ci;
                $yt = trim((string)($_POST['pro_engage_card_yt'] ?? ''));
                if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $yt, $m)) {
                    $p['card_youtube_id'] = $m[1];
                } elseif (preg_match('/^[a-zA-Z0-9_-]{11}$/', $yt)) {
                    $p['card_youtube_id'] = $yt;
                }
                $mp4 = trim((string)($_POST['pro_engage_card_mp4'] ?? ''));
                if ($mp4 !== '') $p['card_mp4'] = $mp4;
                $poster = trim((string)($_POST['pro_engage_card_poster'] ?? ''));
                if ($poster !== '') $p['card_poster'] = $poster;
            }
            if ($id === 'p-2-5') {
                $h = trim((string)($_POST['pro_yt_heading'] ?? ''));
                if ($h !== '') $p['heading'] = $h;
                $yt = trim((string)($_POST['pro_yt_id'] ?? ''));
                if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $yt, $m)) {
                    $p['youtube_id'] = $m[1];
                } elseif (preg_match('/^[a-zA-Z0-9_-]{11}$/', $yt)) {
                    $p['youtube_id'] = $yt;
                }
            }
            if ($id === 'p-2-9') {
                $h = trim((string)($_POST['pro_gallery_heading'] ?? ''));
                if ($h !== '') $p['heading'] = $h;
                $slides = is_array($p['slides'] ?? null) ? $p['slides'] : [[], [], []];
                for ($i = 0; $i < 3; $i++) {
                    if (!is_array($slides[$i] ?? null)) $slides[$i] = [];
                    $img = trim((string)($_POST["pro_slide_image_$i"] ?? ''));
                    $tit = trim((string)($_POST["pro_slide_title_$i"] ?? ''));
                    if ($img !== '') $slides[$i]['image'] = $img;
                    if ($tit !== '') $slides[$i]['title'] = $tit;
                }
                $p['slides'] = $slides;
            }
        }
        unset($b);
    });

    /* ── Institutions structured fields ───────────────────────────── */
    xr_admin_save_page_blocks($out, 'institutions', function (array &$blocks): void {
        foreach ($blocks as &$b) {
            if (!is_array($b)) continue;
            $id = (string)($b['id'] ?? '');
            if (!isset($b['props']) || !is_array($b['props'])) $b['props'] = [];
            $p = &$b['props'];

            if ($id === 'i-3-1') {
                $h = trim((string)($_POST['inst_carousel_heading'] ?? ''));
                if ($h !== '') $p['heading'] = $h;
                $slides = is_array($p['slides'] ?? null) ? $p['slides'] : [[], [], []];
                for ($i = 0; $i < 3; $i++) {
                    if (!is_array($slides[$i] ?? null)) $slides[$i] = [];
                    $img = trim((string)($_POST["inst_carousel_image_$i"] ?? ''));
                    $tit = trim((string)($_POST["inst_carousel_title_$i"] ?? ''));
                    if ($img !== '') $slides[$i]['image'] = $img;
                    if ($tit !== '') $slides[$i]['title'] = $tit;
                }
                $p['slides'] = $slides;
            }
            if ($id === 'i-3-21-24') {
                $h = trim((string)($_POST['inst_gallery_heading'] ?? ''));
                if ($h !== '') $p['heading'] = $h;
                $slides = is_array($p['slides'] ?? null) ? $p['slides'] : [[], [], []];
                for ($i = 0; $i < 3; $i++) {
                    if (!is_array($slides[$i] ?? null)) $slides[$i] = [];
                    $img = trim((string)($_POST["inst_gallery_image_$i"] ?? ''));
                    $tit = trim((string)($_POST["inst_gallery_title_$i"] ?? ''));
                    if ($img !== '') $slides[$i]['image'] = $img;
                    if ($tit !== '') $slides[$i]['title'] = $tit;
                }
                $p['slides'] = $slides;
            }
        }
        unset($b);
    });

    /* ── Blog structured fields ────────────────────────────────────── */
    xr_admin_save_page_blocks($out, 'blog', function (array &$blocks): void {
        foreach ($blocks as &$b) {
            if (!is_array($b)) continue;
            $id = (string)($b['id'] ?? '');
            if (!isset($b['props']) || !is_array($b['props'])) $b['props'] = [];
            $p = &$b['props'];

            if ($id === 'block-4-1') {
                $img = trim((string)($_POST['blog_hero_image'] ?? ''));
                if ($img !== '') $p['image'] = $img;
                $t = trim((string)($_POST['blog_hero_title'] ?? ''));
                if ($t !== '') $p['title'] = $t;
                $s = trim((string)($_POST['blog_hero_subtitle'] ?? ''));
                if ($s !== '') $p['subtitle'] = $s;
            }
            if ($id === 'block-4-2-grid') {
                $posts = is_array($p['posts'] ?? null) ? $p['posts'] : [];
                for ($i = 0; $i < 5; $i++) {
                    if (!is_array($posts[$i] ?? null)) $posts[$i] = [];
                    $img = trim((string)($_POST["blog_post_image_$i"] ?? ''));
                    $tit = trim((string)($_POST["blog_post_title_$i"] ?? ''));
                    $exc = trim((string)($_POST["blog_post_excerpt_$i"] ?? ''));
                    if ($img !== '') $posts[$i]['image'] = $img;
                    if ($tit !== '') $posts[$i]['title'] = $tit;
                    if ($exc !== '') $posts[$i]['excerpt'] = $exc;
                }
                $p['posts'] = $posts;
            }
        }
        unset($b);
    });

    /* ── Partners structured fields ────────────────────────────────── */
    xr_admin_save_page_blocks($out, 'partners', function (array &$blocks): void {
        foreach ($blocks as &$b) {
            if (!is_array($b)) continue;
            $id = (string)($b['id'] ?? '');
            if (!isset($b['props']) || !is_array($b['props'])) $b['props'] = [];
            $p = &$b['props'];

            if ($id === 'block-5-1') {
                $img = trim((string)($_POST['par_hero_image'] ?? ''));
                if ($img !== '') $p['image'] = $img;
                $t = trim((string)($_POST['par_hero_title'] ?? ''));
                if ($t !== '') $p['title'] = $t;
                $s = trim((string)($_POST['par_hero_subtitle'] ?? ''));
                if ($s !== '') $p['subtitle'] = $s;
            }
            if ($id === 'block-5-4') {
                $t = trim((string)($_POST['par_sv1_title'] ?? ''));
                if ($t !== '') $p['title'] = $t;
                $body = trim((string)($_POST['par_sv1_body'] ?? ''));
                if ($body !== '') $p['body'] = $body;
                $poster = trim((string)($_POST['par_sv1_poster'] ?? ''));
                if ($poster !== '') $p['poster'] = $poster;
                $mp4 = trim((string)($_POST['par_sv1_mp4'] ?? ''));
                if ($mp4 !== '') $p['mp4'] = $mp4;
            }
            if ($id === 'block-5-8') {
                $t = trim((string)($_POST['par_sv2_title'] ?? ''));
                if ($t !== '') $p['title'] = $t;
                $body = trim((string)($_POST['par_sv2_body'] ?? ''));
                if ($body !== '') $p['body'] = $body;
                $poster = trim((string)($_POST['par_sv2_poster'] ?? ''));
                if ($poster !== '') $p['poster'] = $poster;
                $mp4 = trim((string)($_POST['par_sv2_mp4'] ?? ''));
                if ($mp4 !== '') $p['mp4'] = $mp4;
            }
            if ($id === 'block-5-5') {
                $t = trim((string)($_POST['par_icons_title'] ?? ''));
                if ($t !== '') $p['title'] = $t;
                $items = is_array($p['items'] ?? null) ? $p['items'] : [];
                for ($i = 0; $i < 4; $i++) {
                    if (!is_array($items[$i] ?? null)) $items[$i] = [];
                    $lbl  = trim((string)($_POST["par_icon_label_$i"] ?? ''));
                    $icon = trim((string)($_POST["par_icon_dashicon_$i"] ?? ''));
                    $txt  = trim((string)($_POST["par_icon_text_$i"] ?? ''));
                    if ($lbl !== '')  $items[$i]['label']    = $lbl;
                    if ($icon !== '') $items[$i]['dashicon'] = $icon;
                    if ($txt !== '')  $items[$i]['text']     = $txt;
                }
                $p['items'] = $items;
            }
        }
        unset($b);
    });

    /* ── JSON overrides (raw editor fallback) ─────────────────────── */
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
