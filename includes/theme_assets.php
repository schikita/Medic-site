<?php

declare(strict_types=1);

/**
 * Картинки из макета: положите файл в public/assets/img/figma/{page}/{basename}.(webp|jpg|jpeg|png)
 * Пока файла нет — используется тот же picsum, что и раньше (стабильный seed).
 *
 * @param non-empty-string $page      home | professionals | institutions | blog | partners
 * @param non-empty-string $basename  латиница, цифры, дефис (имя без расширения)
 * @param non-empty-string $picsumSeed стабильный seed для picsum.photos
 */
function xr_figma_asset(string $page, string $basename, string $picsumSeed, int $w, int $h): string
{
    $page = strtolower(preg_replace('/[^a-z0-9_-]/i', '', $page));
    if ($page === '') {
        $page = 'misc';
    }
    $basename = strtolower(preg_replace('/[^a-z0-9_-]/i', '', $basename));
    if ($basename === '') {
        $basename = 'asset';
    }
    $picsumSeed = preg_replace('/[^a-zA-Z0-9_-]/', '', $picsumSeed) ?: 'xr';

    $dir = ROOT . '/public/assets/img/figma/' . $page;
    $pub = '/assets/img/figma/' . $page;
    foreach (['.webp', '.jpg', '.jpeg', '.png'] as $ext) {
        $fs = $dir . '/' . $basename . $ext;
        if (is_readable($fs)) {
            return $pub . '/' . $basename . $ext;
        }
    }

    $w = max(1, min(4000, $w));
    $h = max(1, min(4000, $h));

    return 'https://picsum.photos/seed/' . rawurlencode($picsumSeed) . '/' . $w . '/' . $h;
}
