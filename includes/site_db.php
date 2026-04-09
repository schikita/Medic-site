<?php

declare(strict_types=1);

/** @var list<string> */
const XR_PAGE_SLUGS = ['home', 'professionals', 'institutions', 'blog', 'partners'];

function xr_site_uses_database(): bool
{
    return extension_loaded('pdo_sqlite');
}

function xr_site_db_path(): string
{
    return ROOT . '/data/site.sqlite';
}

function xr_db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }
    $path = xr_site_db_path();
    $pdo = new PDO('sqlite:' . $path, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $pdo->exec('PRAGMA foreign_keys = ON');

    return $pdo;
}

function xr_db_column_exists(PDO $pdo, string $table, string $column): bool
{
    static $cache = [];
    $key = $table . "\0" . $column;
    if (isset($cache[$key])) {
        return $cache[$key];
    }
    if ($table !== 'site_settings' && $table !== 'pages') {
        return false;
    }
    $stmt = $pdo->query('PRAGMA table_info(' . $table . ')');
    if (!$stmt) {
        return false;
    }
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if (($row['name'] ?? '') === $column) {
            $cache[$key] = true;

            return true;
        }
    }
    $cache[$key] = false;

    return false;
}

function xr_db_migrate_seo_columns(PDO $pdo): void
{
    if (!xr_db_column_exists($pdo, 'site_settings', 'seo_json')) {
        $pdo->exec('ALTER TABLE site_settings ADD COLUMN seo_json TEXT NOT NULL DEFAULT \'{}\'');
    }
    if (!xr_db_column_exists($pdo, 'pages', 'meta_extra_json')) {
        $pdo->exec('ALTER TABLE pages ADD COLUMN meta_extra_json TEXT NOT NULL DEFAULT \'{}\'');
    }
}

function xr_db_exec_schema(PDO $pdo): void
{
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS site_settings (
            id INTEGER PRIMARY KEY CHECK (id = 1),
            hubspot_json TEXT NOT NULL DEFAULT "{}",
            nav_json TEXT NOT NULL DEFAULT "{}"
        )'
    );
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS pages (
            slug TEXT PRIMARY KEY,
            meta_title TEXT NOT NULL DEFAULT "",
            meta_description TEXT NOT NULL DEFAULT "",
            blocks_json TEXT NOT NULL DEFAULT "[]"
        )'
    );
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS media (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            storage TEXT NOT NULL DEFAULT "local",
            public_path TEXT NOT NULL UNIQUE,
            mime TEXT,
            size_bytes INTEGER,
            width INTEGER,
            height INTEGER,
            duration_sec REAL,
            alt TEXT,
            title TEXT,
            original_name TEXT,
            created_at TEXT NOT NULL DEFAULT ""
        )'
    );
    $pdo->exec('CREATE INDEX IF NOT EXISTS idx_media_created ON media (created_at DESC)');
    xr_db_migrate_seo_columns($pdo);
}

function xr_site_seed_array_for_install(): array
{
    $def = default_site();
    $path = site_data_path();
    if (!is_readable($path)) {
        return $def;
    }
    $raw = json_decode((string) file_get_contents($path), true);
    if (!is_array($raw)) {
        return $def;
    }
    if (!empty($raw['home']['blocks']) && is_array($raw['home']['blocks'])) {
        return array_replace_recursive($def, $raw);
    }

    return migrate_legacy_to_full($raw);
}

function xr_site_db_install(): void
{
    $dir = dirname(xr_site_db_path());
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $pdo = new PDO('sqlite:' . xr_site_db_path(), null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $pdo->exec('PRAGMA foreign_keys = ON');
    xr_db_exec_schema($pdo);
    xr_db_persist_site($pdo, xr_site_seed_array_for_install());
}

function xr_site_db_ensure_installed(): void
{
    if (!xr_site_uses_database()) {
        return;
    }
    if (!is_readable(xr_site_db_path())) {
        xr_site_db_install();

        return;
    }
    try {
        $pdo = xr_db();
        // Always run full schema + migrations (all CREATE IF NOT EXISTS — safe to repeat)
        xr_db_exec_schema($pdo);
        $pdo->query('SELECT 1 FROM site_settings WHERE id = 1')->fetchColumn();
        $n = (int) $pdo->query('SELECT COUNT(*) FROM pages')->fetchColumn();
        if ($n === 0) {
            xr_db_persist_site($pdo, xr_site_seed_array_for_install());
        }
        xr_db_migrate_orbit_i38_planning_label($pdo);
    } catch (Throwable $e) {
        xr_site_db_install();
    }
}

/**
 * Content fixes for i-3-8 orbit cards when DB overrides site.json (same labels as merged defaults).
 * Idempotent after each swap.
 */
function xr_db_migrate_orbit_i38_planning_label(PDO $pdo): void
{
    $stmt = $pdo->prepare('SELECT blocks_json FROM pages WHERE slug = ?');
    $stmt->execute(['institutions']);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!is_array($row)) {
        return;
    }
    $blocks = json_decode((string) ($row['blocks_json'] ?? '[]'), true);
    if (!is_array($blocks)) {
        return;
    }
    $changed = false;
    foreach ($blocks as $i => $b) {
        if (!is_array($b) || ($b['id'] ?? '') !== 'i-3-8') {
            continue;
        }
        $cards = $b['props']['cards'] ?? null;
        if (!is_array($cards)) {
            break;
        }
        if (isset($cards[0]) && is_array($cards[0])) {
            $lab = trim((string) ($cards[0]['label'] ?? ''));
            if (strcasecmp($lab, 'SSO') === 0) {
                $blocks[$i]['props']['cards'][0]['label'] = "Team\nWork";
                $blocks[$i]['props']['cards'][0]['sub'] = 'SSO';
                $changed = true;
            }
        }
        if (isset($cards[1]) && is_array($cards[1])) {
            $lab = trim((string) ($cards[1]['label'] ?? ''));
            if (strcasecmp($lab, 'LMS') === 0) {
                $blocks[$i]['props']['cards'][1]['label'] = 'Planning';
                $blocks[$i]['props']['cards'][1]['sub'] = 'LMS';
                $changed = true;
            }
        }
        if (isset($cards[2]) && is_array($cards[2])) {
            $lab = trim((string) ($cards[2]['label'] ?? ''));
            if (strcasecmp($lab, 'Audit') === 0) {
                $blocks[$i]['props']['cards'][2]['label'] = 'Diagnostic';
                $blocks[$i]['props']['cards'][2]['sub'] = 'Audit';
                $changed = true;
            }
        }
        if (isset($cards[3]) && is_array($cards[3])) {
            $lab = trim((string) ($cards[3]['label'] ?? ''));
            if (strcasecmp($lab, 'SLA') === 0) {
                $blocks[$i]['props']['cards'][3]['label'] = 'Execution';
                $blocks[$i]['props']['cards'][3]['sub'] = 'SLA';
                $changed = true;
            }
        }
        if (isset($cards[4]) && is_array($cards[4])) {
            $lab = trim((string) ($cards[4]['label'] ?? ''));
            if (strcasecmp($lab, 'Outcome Analysis') === 0) {
                $blocks[$i]['props']['cards'][4]['label'] = "Tele-\nMentoring";
                $blocks[$i]['props']['cards'][4]['sub'] = 'Outcome Analysis';
                $changed = true;
            }
        }
        if (isset($cards[5]) && is_array($cards[5])) {
            $lab = trim((string) ($cards[5]['label'] ?? ''));
            if (strcasecmp($lab, 'Patient Education') === 0) {
                $blocks[$i]['props']['cards'][5]['label'] = "Case\nRecords";
                $blocks[$i]['props']['cards'][5]['sub'] = 'Patient Education';
                $changed = true;
            }
        }
        if (isset($cards[6]) && is_array($cards[6])) {
            $lab = trim((string) ($cards[6]['label'] ?? ''));
            if (strcasecmp($lab, 'Team Work') === 0) {
                $blocks[$i]['props']['cards'][6]['label'] = "Outcome\nAnalysis";
                $blocks[$i]['props']['cards'][6]['sub'] = 'Team Work';
                $changed = true;
            }
        }
        if (isset($cards[7]) && is_array($cards[7])) {
            $lab = trim((string) ($cards[7]['label'] ?? ''));
            if (strcasecmp($lab, 'Planning') === 0) {
                $blocks[$i]['props']['cards'][7]['label'] = "Patient\nEducation";
                $blocks[$i]['props']['cards'][7]['sub'] = 'Planning';
                $changed = true;
            }
        }
        break;
    }
    if (!$changed) {
        return;
    }
    $json = json_encode($blocks, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        return;
    }
    $up = $pdo->prepare('UPDATE pages SET blocks_json = ? WHERE slug = ?');
    $up->execute([$json, 'institutions']);
}

function load_site_from_json_only(array $def): array
{
    $path = site_data_path();
    if (!is_readable($path)) {
        return $def;
    }
    $raw = json_decode((string) file_get_contents($path), true);
    if (!is_array($raw)) {
        return $def;
    }
    if (!empty($raw['home']['blocks']) && is_array($raw['home']['blocks'])) {
        return array_replace_recursive($def, $raw);
    }

    return migrate_legacy_to_full($raw);
}

function xr_db_load_site_merged(array $def, PDO $pdo): array
{
    $site = $def;
    $st = $pdo->query('SELECT hubspot_json, nav_json, seo_json FROM site_settings WHERE id = 1')->fetch(PDO::FETCH_ASSOC);
    if (is_array($st)) {
        $h = json_decode((string) ($st['hubspot_json'] ?? '{}'), true);
        $n = json_decode((string) ($st['nav_json'] ?? '{}'), true);
        if (is_array($h)) {
            $site['hubspot'] = array_replace_recursive($def['hubspot'], $h);
        }
        if (is_array($n)) {
            $site['nav'] = array_replace_recursive($def['nav'], $n);
        }
        $sj = json_decode((string) ($st['seo_json'] ?? '{}'), true);
        if (is_array($sj)) {
            $site['seo'] = array_replace_recursive($def['seo'] ?? xr_site_seo_defaults(), $sj);
        }
    }
    $stmt = $pdo->prepare('SELECT meta_title, meta_description, blocks_json, meta_extra_json FROM pages WHERE slug = ?');
    foreach (XR_PAGE_SLUGS as $slug) {
        if (!isset($def[$slug])) {
            continue;
        }
        $stmt->execute([$slug]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!is_array($r)) {
            continue;
        }
        $blocks = json_decode((string) ($r['blocks_json'] ?? '[]'), true);
        if (!is_array($blocks)) {
            $blocks = [];
        }
        $base = is_array($def[$slug]['meta'] ?? null) ? $def[$slug]['meta'] : xr_page_meta_schema();
        $extra = json_decode((string) ($r['meta_extra_json'] ?? '{}'), true);
        if (!is_array($extra)) {
            $extra = [];
        }
        $merged = array_replace_recursive($base, $extra);
        $mt = trim((string) ($r['meta_title'] ?? ''));
        $md = trim((string) ($r['meta_description'] ?? ''));
        if ($mt !== '') {
            $merged['title'] = $mt;
        }
        if ($md !== '') {
            $merged['description'] = $md;
        }
        $site[$slug]['meta'] = $merged;
        if ($blocks !== []) {
            $site[$slug]['blocks'] = $blocks;
        }
    }

    return $site;
}

function xr_db_persist_site(PDO $pdo, array $site): bool
{
    try {
        $pdo->beginTransaction();
        $hub = json_encode($site['hubspot'] ?? [], JSON_UNESCAPED_UNICODE);
        $nav = json_encode($site['nav'] ?? [], JSON_UNESCAPED_UNICODE);
        $seo = json_encode($site['seo'] ?? [], JSON_UNESCAPED_UNICODE);
        if ($hub === false || $nav === false || $seo === false) {
            throw new RuntimeException('json encode');
        }
        $pdo->prepare(
            'INSERT INTO site_settings (id, hubspot_json, nav_json, seo_json) VALUES (1, ?, ?, ?)
             ON CONFLICT(id) DO UPDATE SET hubspot_json = excluded.hubspot_json,
               nav_json = excluded.nav_json, seo_json = excluded.seo_json'
        )->execute([$hub, $nav, $seo]);

        $up = $pdo->prepare(
            'INSERT INTO pages (slug, meta_title, meta_description, blocks_json, meta_extra_json) VALUES (?, ?, ?, ?, ?)
             ON CONFLICT(slug) DO UPDATE SET meta_title = excluded.meta_title,
               meta_description = excluded.meta_description, blocks_json = excluded.blocks_json,
               meta_extra_json = excluded.meta_extra_json'
        );
        foreach (XR_PAGE_SLUGS as $slug) {
            if (!isset($site[$slug]) || !is_array($site[$slug])) {
                continue;
            }
            $meta = xr_normalize_page_meta(is_array($site[$slug]['meta'] ?? null) ? $site[$slug]['meta'] : []);
            $blocks = $site[$slug]['blocks'] ?? [];
            if (!is_array($blocks)) {
                $blocks = [];
            }
            $bj = json_encode($blocks, JSON_UNESCAPED_UNICODE);
            if ($bj === false) {
                throw new RuntimeException('blocks json');
            }
            $extra = $meta;
            unset($extra['title'], $extra['description']);
            $extraJson = json_encode($extra, JSON_UNESCAPED_UNICODE);
            if ($extraJson === false) {
                throw new RuntimeException('meta extra json');
            }
            $up->execute([
                $slug,
                (string) ($meta['title'] ?? ''),
                (string) ($meta['description'] ?? ''),
                $bj,
                $extraJson,
            ]);
        }
        $pdo->commit();

        return true;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        return false;
    }
}

function xr_site_sync_json_backup(array $site): void
{
    $dir = dirname(site_data_path());
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $payload = json_encode($site, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if ($payload !== false) {
        @file_put_contents(site_data_path(), $payload, LOCK_EX);
    }
}

function xr_media_allowed_mimes(): array
{
    return [
        'image/jpeg' => '.jpg',
        'image/png' => '.png',
        'image/webp' => '.webp',
        'image/gif' => '.gif',
        'video/mp4' => '.mp4',
        'video/webm' => '.webm',
    ];
}

function xr_media_max_upload_bytes(): int
{
    return 100 * 1024 * 1024;
}

/**
 * @return array<string, mixed>|null Row as associative array or null on failure
 */
function xr_media_save_upload(array $file): ?array
{
    if (!xr_site_uses_database() || !is_readable(xr_site_db_path())) {
        return null;
    }
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }
    if (!is_uploaded_file((string) ($file['tmp_name'] ?? ''))) {
        return null;
    }
    $max = xr_media_max_upload_bytes();
    $size = (int) ($file['size'] ?? 0);
    if ($size <= 0 || $size > $max) {
        return null;
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file((string) $file['tmp_name']) ?: '';
    $map = xr_media_allowed_mimes();
    if (!isset($map[$mime])) {
        return null;
    }
    $ext = $map[$mime];
    $sub = gmdate('Y/m');
    $baseDir = ROOT . '/public/uploads/' . $sub;
    if (!is_dir($baseDir)) {
        mkdir($baseDir, 0755, true);
    }
    $basename = bin2hex(random_bytes(12)) . $ext;
    $destFs = $baseDir . '/' . $basename;
    if (!move_uploaded_file((string) $file['tmp_name'], $destFs)) {
        return null;
    }
    $publicPath = '/uploads/' . $sub . '/' . $basename;
    $width = null;
    $height = null;
    if (str_starts_with($mime, 'image/')) {
        $dims = @getimagesize($destFs);
        if (is_array($dims)) {
            $width = (int) ($dims[0] ?? 0) ?: null;
            $height = (int) ($dims[1] ?? 0) ?: null;
        }
    }
    $orig = (string) ($file['name'] ?? '');
    if (strlen($orig) > 240) {
        $orig = substr($orig, 0, 240);
    }

    xr_site_db_ensure_installed();
    $pdo = xr_db();
    $stmt = $pdo->prepare(
        'INSERT INTO media (storage, public_path, mime, size_bytes, width, height, original_name, created_at)
         VALUES ("local", ?, ?, ?, ?, ?, ?, datetime("now"))'
    );
    $stmt->execute([$publicPath, $mime, $size, $width, $height, $orig]);
    $id = (int) $pdo->lastInsertId();

    return [
        'id' => $id,
        'public_path' => $publicPath,
        'mime' => $mime,
        'size_bytes' => $size,
        'width' => $width,
        'height' => $height,
        'original_name' => $orig,
    ];
}

/**
 * @return list<array<string, mixed>>
 */
function xr_media_list(int $limit = 200): array
{
    if (!xr_site_uses_database() || !is_readable(xr_site_db_path())) {
        return [];
    }
    try {
        $pdo = xr_db();
        $stmt = $pdo->prepare('SELECT * FROM media ORDER BY id DESC LIMIT ?');
        $stmt->bindValue(1, max(1, min(500, $limit)), PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (Throwable $e) {
        return [];
    }
}

/**
 * Merge saved page blocks with defaults by block id so new props from PHP defaults
 * appear even when SQLite/JSON was saved before those keys existed.
 *
 * @param list<array<string, mixed>> $defaultBlocks
 * @param list<array<string, mixed>> $savedBlocks
 * @return list<array<string, mixed>>
 */
function xr_merge_page_blocks_with_defaults(array $defaultBlocks, array $savedBlocks): array
{
    $defById = [];
    foreach ($defaultBlocks as $b) {
        if (is_array($b) && isset($b['id'])) {
            $defById[(string) $b['id']] = $b;
        }
    }
    $out = [];
    foreach ($savedBlocks as $b) {
        if (!is_array($b)) {
            continue;
        }
        $id = (string) ($b['id'] ?? '');
        if ($id !== '' && isset($defById[$id])) {
            $out[] = array_replace_recursive($defById[$id], $b);
        } else {
            $out[] = $b;
        }
    }

    return $out;
}

/**
 * @param array<string, mixed> $def  from default_site()
 * @param array<string, mixed> $site merged site array
 * @return array<string, mixed>
 */
function xr_site_merge_blocks_with_defaults(array $def, array $site): array
{
    foreach (XR_PAGE_SLUGS as $slug) {
        $defBlocks = $def[$slug]['blocks'] ?? null;
        $savedBlocks = $site[$slug]['blocks'] ?? null;
        if (!is_array($defBlocks) || $defBlocks === [] || !is_array($savedBlocks) || $savedBlocks === []) {
            continue;
        }
        $site[$slug]['blocks'] = xr_merge_page_blocks_with_defaults($defBlocks, $savedBlocks);
    }

    return $site;
}
