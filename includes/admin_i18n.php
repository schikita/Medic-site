<?php

declare(strict_types=1);

const ADMIN_UI_COOKIE = 'admin_ui_lang';

/**
 * UI language for admin: Russian (default) or English.
 */
function admin_lang(): string
{
    if (isset($_SESSION['admin_lang']) && ($_SESSION['admin_lang'] === 'ru' || $_SESSION['admin_lang'] === 'en')) {
        return $_SESSION['admin_lang'];
    }
    $c = (string) ($_COOKIE[ADMIN_UI_COOKIE] ?? '');
    if ($c === 'en' || $c === 'ru') {
        $_SESSION['admin_lang'] = $c;

        return $c;
    }

    return 'ru';
}

function admin_set_lang(string $lang): void
{
    if ($lang !== 'ru' && $lang !== 'en') {
        return;
    }
    $_SESSION['admin_lang'] = $lang;
    $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    setcookie(ADMIN_UI_COOKIE, $lang, [
        'expires' => time() + 365 * 86400,
        'path' => '/',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function admin_safe_return(?string $url, string $default = '/admin/'): string
{
    if ($url === null || $url === '') {
        return $default;
    }
    if (!str_starts_with($url, '/') || str_starts_with($url, '//')) {
        return $default;
    }
    if (str_contains($url, "\0") || str_contains($url, '..')) {
        return $default;
    }

    return $url;
}

/**
 * @param array<string, string> $vars Placeholders {key} in string
 */
function admin_t(string $key, array $vars = []): string
{
    static $ru = null;
    static $en = null;
    if ($ru === null) {
        $ru = admin_i18n_strings_ru();
        $en = admin_i18n_strings_en();
    }
    $dict = admin_lang() === 'en' ? $en : $ru;
    $s = $dict[$key] ?? $key;
    foreach ($vars as $k => $v) {
        $s = str_replace('{' . $k . '}', (string) $v, $s);
    }

    return $s;
}

function admin_lang_switch_url(string $lang, string $returnPath = ''): string
{
    $ret = $returnPath !== '' ? $returnPath : (string) ($_SERVER['REQUEST_URI'] ?? '/admin/');

    return '/admin/lang_switch.php?lang=' . rawurlencode($lang) . '&return=' . rawurlencode(admin_safe_return($ret));
}

/** @return array<string, string> */
function admin_i18n_strings_ru(): array
{
    return [
        'lang.ru' => 'Русский',
        'lang.en' => 'English',
        'lang.label' => 'Язык интерфейса',

        'page.main_title' => 'Редактирование · Админка',
        'page.login_title' => 'Вход · Админка',
        'page.media_title' => 'Медиатека · Админка',

        'nav.brand' => 'Контент сайта',
        'nav.preview_home' => 'Главная',
        'nav.preview_institutions' => 'Institutions',
        'nav.preview_professionals' => 'Professionals',
        'nav.preview_blog' => 'Blog',
        'nav.preview_partners' => 'Partners',
        'nav.media' => 'Медиатека',
        'nav.logout' => 'Выйти',
        'nav.content' => 'Контент',
        'nav.site' => 'Сайт',

        'toc.label' => 'Разделы',
        'toc.seo_global' => 'SEO: весь сайт',
        'toc.seo_pages' => 'SEO: страницы',
        'toc.hubspot' => 'HubSpot',
        'toc.header' => 'Шапка и меню',
        'toc.home' => 'Главная: блоки',
        'toc.json' => 'JSON блоков',

        'section.seo_global' => 'SEO для всего сайта',
        'section.seo_pages' => 'SEO по страницам',
        'section.hubspot' => 'HubSpot (модальное окно)',
        'section.header' => 'Шапка и меню',
        'section.home_blocks' => 'Главная страница — ключевые блоки',
        'section.json' => 'Расширенное редактирование (JSON)',

        'hint.seo_global' => 'Эти поля попадают в публичные метатеги, Open Graph, Twitter Cards и JSON-LD (тексты для поиска и соцсетей обычно на английском). Поле «URL сайта» оставьте пустым — домен подставится автоматически (удобно для локальной проверки).',
        'hint.hubspot' => 'Полные URL встраиваемых форм HubSpot. Кнопки White Paper и Request Demo открывают модальное окно с iframe.',
        'hint.nav_pages' => 'Активный пункт меню задаётся полем page в данных (home, professionals, institutions, blog, partners). Типичные ссылки: /, /professionals.php, /institutions.php, /blog.php, /partners.php.',
        'hint.assistant' => 'Здесь редактируется первая вкладка блока продуктов. Остальные вкладки — через JSON внизу страницы или значения по умолчанию.',
        'hint.json' => 'Если заполнить, при сохранении полностью заменяются блоки соответствующей страницы. Оставьте пустым, чтобы использовались только поля выше.',

        'seo.site_url' => 'URL сайта (канонический origin)',
        'seo.site_name' => 'Название / бренд сайта',
        'seo.append_title' => 'Добавлять название сайта к &lt;title&gt;',
        'seo.append_title_help' => 'Формат: заголовок страницы · название сайта (если бренд ещё не в заголовке)',
        'seo.title_sep' => 'Разделитель в заголовке',
        'seo.html_lang' => 'Язык HTML (атрибут lang)',
        'seo.og_locale' => 'Локаль Open Graph',
        'seo.default_og_image' => 'Картинка по умолчанию для шаринга (путь или полный URL)',
        'seo.twitter_site' => 'Twitter: @сайт (без @)',
        'seo.twitter_creator' => 'Twitter: @автор (без @)',
        'seo.fb_app_id' => 'Facebook App ID (необязательно)',
        'seo.org_block' => 'Организация (JSON-LD)',
        'seo.org_name' => 'Название организации',
        'seo.org_url' => 'Сайт организации (путь или URL)',
        'seo.org_logo' => 'Логотип (путь или URL)',

        'page.home' => 'Главная',
        'page.professionals' => 'Professionals',
        'page.institutions' => 'Institutions',
        'page.blog' => 'Блог',
        'page.partners' => 'Партнёрам',

        'meta.doc_title' => 'Заголовок страницы (&lt;title&gt;)',
        'meta.description' => 'Meta description',
        'meta.keywords' => 'Ключевые слова (через запятую, необязательно)',
        'meta.robots' => 'Robots (пусто = индексация и расширенные подсказки Google)',
        'meta.robots_ph' => 'например: noindex, nofollow — только при необходимости',
        'meta.canonical' => 'Свой путь canonical (необязательно)',
        'meta.canonical_ph' => 'По умолчанию:',
        'meta.og_block' => 'Open Graph / Twitter (если пусто — подставятся заголовок и описание выше)',
        'meta.og_title' => 'Заголовок og:title',
        'meta.og_image' => 'Картинка og:image',
        'meta.og_type' => 'Тип og:type',
        'meta.og_desc' => 'Описание og:description',
        'meta.tw_card' => 'twitter:card',

        'hubspot.whitepaper_url' => 'White Paper — URL формы',
        'hubspot.demo_url' => 'Request Demo — URL формы',

        'nav.logo_alt' => 'Альтернативный текст логотипа',
        'nav.items' => 'Пункты меню',
        'nav.label_ph' => 'Текст',
        'nav.href_ph' => 'URL',
        'nav.new_item' => 'Новый пункт',
        'nav.cta_wp_label' => 'Кнопка White Paper — текст',
        'nav.cta_wp_href' => 'Ссылка (можно #hubspot-whitepaper или #)',
        'nav.cta_demo_label' => 'Кнопка Request Demo — текст',
        'nav.cta_demo_href' => 'Ссылка',

        'hero.legend' => 'Hero (блок 1.1)',
        'hero.youtube' => 'YouTube ID или URL фонового видео (приоритет над MP4)',
        'hero.poster' => 'Путь к постеру / картинке',
        'hero.mp4' => 'URL видео MP4 (необязательно)',
        'hero.webm' => 'URL видео WebM (необязательно)',
        'hero.overlay_note' => 'Подсказка над жёлтыми строками',
        'hero.overlay_lines' => 'Жёлтые строки (каждая с новой строки)',

        'intro.legend' => 'Градиентный заголовок (блок 1.2)',
        'intro.eyebrow' => 'Верхняя строка',
        'intro.line1' => 'Строка 1',
        'intro.line2' => 'Строка 2',
        'intro.body' => 'Текст (абзац)',
        'intro.tagline' => 'Подзаголовок',

        'oculus.legend' => 'Очки · три вкладки YouTube (блок 1.4–1.5)',
        'oculus.hint' => 'Для каждой кнопки — постер и ID ролика YouTube (без youtube.com). Тексты вкладок задаются в JSON блока, если нужно.',
        'oculus.tab_n' => 'Вкладка {n}',
        'oculus.poster' => 'Путь к постеру',
        'oculus.youtube_id' => 'ID YouTube',

        'assistant.legend'       => 'Продукт · вкладки (блоки 1.7–1.8)',
        'assistant.video_yt'     => 'YouTube ID или URL видео-слайда',
        'assistant.video_poster' => 'Постер видео-слайда (картинка до Play)',
        'assistant.video_label'  => 'Текст над кнопкой Play',
        'assistant.card_img_0'   => 'Карточка 1 — картинка (URL или путь)',
        'assistant.card_img_1'   => 'Карточка 2 — картинка (URL или путь)',
        'assistant.grid_icons'   => 'Иконки AI-слайда (4 ячейки)',
        'assistant.grid_icon_0'  => 'AI Suite — иконка',
        'assistant.grid_icon_1'  => 'Clinical Assistant — иконка',
        'assistant.grid_icon_2'  => 'Training Coach — иконка',
        'assistant.grid_icon_3'  => 'Teamwork Mentor — иконка',
        'assistant.tabs' => 'Названия вкладок (каждая с новой строки)',
        'assistant.active' => 'Индекс активной вкладки (0 = первая)',
        'assistant.title' => 'Заголовок (первая вкладка)',
        'assistant.lead' => 'Основной текст',
        'assistant.p2' => 'Абзац 2',
        'assistant.p3' => 'Акцентный абзац',
        'assistant.sidebar' => 'Текст справа',
        'assistant.features_hint' => 'Список слева',
        'assistant.feature_ph' => 'Добавить пункт',
        'assistant.bottom_title' => 'Заголовок внизу блока',
        'assistant.link_label' => 'Ссылка — текст',
        'assistant.link_href' => 'Ссылка — адрес (href)',

        'vfreeze.legend' => 'Видео-заморозка (блок 1-6)',
        'vfreeze.hint'   => 'Видео воспроизводится автоматически и останавливается на последнем кадре. Можно загрузить файл через Медиатеку и вставить путь сюда.',
        'vfreeze.heading'  => 'Заголовок (строка 1)',
        'vfreeze.heading2' => 'Заголовок (строка 2)',
        'vfreeze.intro'    => 'Вступительный абзац',
        'vfreeze.youtube'  => 'YouTube ID или URL (приоритет над MP4)',
        'vfreeze.mp4'      => 'URL видео MP4 (если не YouTube)',
        'vfreeze.poster'   => 'Путь к постеру (картинка до старта)',
        'vfreeze.caption'  => 'Подпись под видео',

        'closing.legend' => 'Финальный блок',
        'closing.line1' => 'Строка 1',
        'closing.line2' => 'Строка 2',

        'json.legend' => 'JSON всех блоков (необязательно)',
        'json.home' => 'Главная — blocks[]',
        'json.pro' => 'Professionals — blocks[]',
        'json.inst' => 'Institutions — blocks[]',
        'json.blog' => 'Blog — blocks[]',
        'json.partners' => 'Partners — blocks[]',

        'btn.save' => 'Сохранить',
        'btn.login' => 'Войти',
        'btn.upload' => 'Загрузить',

        'login.heading' => 'Админка сайта',
        'login.password' => 'Пароль',
        'login.to_site' => 'На сайт',
        'login.err_no_hash' => 'Задайте password_hash в config.local.php (см. config.local.example.php).',
        'login.err_bad_password' => 'Неверный пароль.',
        'login.hint_config' => 'Создайте config.local.php из примера и добавьте хеш пароля:',
        'login.code_hint' => 'php -r "echo password_hash(\'ваш_пароль\', PASSWORD_DEFAULT);"',

        'media.heading' => 'Медиатека',
        'media.upload_legend' => 'Загрузить фото или видео',
        'media.upload_hint' => 'JPEG, PNG, WebP, GIF до ~100 МБ; видео MP4, WebM. Файлы в public/uploads/ГОД/МЕСЯЦ/, в БД — путь и метаданные.',
        'media.file' => 'Файл',
        'media.no_db' => 'Медиатека доступна при включённом SQLite (pdo_sqlite) и файле data/site.sqlite. Сейчас загрузки отключены.',
        'media.list_legend' => 'Загруженные файлы (скопируйте URL в блоки)',
        'media.empty' => 'Пока пусто.',
        'media.th_id' => 'ID',
        'media.th_preview' => 'Превью / тип',
        'media.th_url' => 'URL',
        'media.th_size' => 'Размер',
        'media.th_name' => 'Имя файла',

        'flash.saved_sqlite' => 'Сохранено (SQLite + резервная копия site.json).',
        'flash.saved_json' => 'Сохранено (data/site.json).',
        'flash.save_error' => 'Ошибка записи — проверьте права на data/ и data/site.sqlite.',
        'flash.media_csrf' => 'Ошибка CSRF.',
        'flash.media_no_file' => 'Файл не передан.',
        'flash.media_fail' => 'Не удалось сохранить файл (тип, размер или БД).',
        'flash.media_ok' => 'Загружено. URL: {path}',

        'disclosure.toggle' => 'Развернуть или свернуть раздел',
    ];
}

/** @return array<string, string> */
function admin_i18n_strings_en(): array
{
    return [
        'lang.ru' => 'Russian',
        'lang.en' => 'English',
        'lang.label' => 'Interface language',

        'page.main_title' => 'Editor · Admin',
        'page.login_title' => 'Sign in · Admin',
        'page.media_title' => 'Media library · Admin',

        'nav.brand' => 'Site content',
        'nav.preview_home' => 'Home',
        'nav.preview_institutions' => 'Institutions',
        'nav.preview_professionals' => 'Professionals',
        'nav.preview_blog' => 'Blog',
        'nav.preview_partners' => 'Partners',
        'nav.media' => 'Media',
        'nav.logout' => 'Log out',
        'nav.content' => 'Content',
        'nav.site' => 'Site',

        'toc.label' => 'Sections',
        'toc.seo_global' => 'SEO: site-wide',
        'toc.seo_pages' => 'SEO: pages',
        'toc.hubspot' => 'HubSpot',
        'toc.header' => 'Header & menu',
        'toc.home' => 'Home: blocks',
        'toc.json' => 'Block JSON',

        'section.seo_global' => 'Site-wide SEO',
        'section.seo_pages' => 'Per-page SEO',
        'section.hubspot' => 'HubSpot (modal)',
        'section.header' => 'Header & navigation',
        'section.home_blocks' => 'Home page — key blocks',
        'section.json' => 'Advanced: block JSON',

        'hint.seo_global' => 'These values feed public meta tags, Open Graph, Twitter Cards, and JSON-LD (search/social copy is usually in English). Leave Site URL empty to auto-detect the host (handy for local testing).',
        'hint.hubspot' => 'Full embed URLs for HubSpot forms. White Paper and Request Demo open a modal with an iframe.',
        'hint.nav_pages' => 'The active menu item uses the page field in data (home, professionals, institutions, blog, partners). Typical hrefs: /, /professionals.php, /institutions.php, /blog.php, /partners.php.',
        'hint.assistant' => 'Only the first product tab is edited here. Other tabs come from the JSON area below or defaults.',
        'hint.json' => 'If filled, saving replaces that page’s blocks entirely. Leave empty to keep using the fields above.',

        'seo.site_url' => 'Site URL (canonical origin)',
        'seo.site_name' => 'Site / brand name',
        'seo.append_title' => 'Append site name to &lt;title&gt;',
        'seo.append_title_help' => 'Format: page title · site name (if the brand is not already in the title)',
        'seo.title_sep' => 'Title separator',
        'seo.html_lang' => 'HTML language (lang)',
        'seo.og_locale' => 'Open Graph locale',
        'seo.default_og_image' => 'Default share image (path or full URL)',
        'seo.twitter_site' => 'Twitter @site (without @)',
        'seo.twitter_creator' => 'Twitter @creator (without @)',
        'seo.fb_app_id' => 'Facebook App ID (optional)',
        'seo.org_block' => 'Organization (JSON-LD)',
        'seo.org_name' => 'Organization name',
        'seo.org_url' => 'Organization URL (path or URL)',
        'seo.org_logo' => 'Logo (path or URL)',

        'page.home' => 'Home',
        'page.professionals' => 'Professionals',
        'page.institutions' => 'Institutions',
        'page.blog' => 'Blog',
        'page.partners' => 'Partners',

        'meta.doc_title' => 'Document title (&lt;title&gt;)',
        'meta.description' => 'Meta description',
        'meta.keywords' => 'Keywords (comma-separated, optional)',
        'meta.robots' => 'Robots (empty = index, follow, Google extended hints)',
        'meta.robots_ph' => 'e.g. noindex, nofollow — only if needed',
        'meta.canonical' => 'Canonical path override (optional)',
        'meta.canonical_ph' => 'Default:',
        'meta.og_block' => 'Open Graph / Twitter (falls back to title & description above)',
        'meta.og_title' => 'og:title override',
        'meta.og_image' => 'og:image',
        'meta.og_type' => 'og:type',
        'meta.og_desc' => 'og:description override',
        'meta.tw_card' => 'twitter:card',

        'hubspot.whitepaper_url' => 'White Paper — form URL',
        'hubspot.demo_url' => 'Request Demo — form URL',

        'nav.logo_alt' => 'Logo alt text',
        'nav.items' => 'Menu items',
        'nav.label_ph' => 'Label',
        'nav.href_ph' => 'URL',
        'nav.new_item' => 'New item',
        'nav.cta_wp_label' => 'White Paper button — label',
        'nav.cta_wp_href' => 'Link (use #hubspot-whitepaper or #)',
        'nav.cta_demo_label' => 'Request Demo button — label',
        'nav.cta_demo_href' => 'Link',

        'hero.legend' => 'Hero (block 1.1)',
        'hero.youtube' => 'YouTube ID or URL for background video (takes priority over MP4)',
        'hero.poster' => 'Poster / image path',
        'hero.mp4' => 'Video MP4 URL (optional)',
        'hero.webm' => 'Video WebM URL (optional)',
        'hero.overlay_note' => 'Note above yellow lines',
        'hero.overlay_lines' => 'Yellow lines (one per line)',

        'intro.legend' => 'Gradient headline (block 1.2)',
        'intro.eyebrow' => 'Eyebrow line',
        'intro.line1' => 'Line 1',
        'intro.line2' => 'Line 2',
        'intro.body' => 'Body paragraph',
        'intro.tagline' => 'Tagline',

        'oculus.legend' => 'Headset · three YouTube tabs (block 1.4–1.5)',
        'oculus.hint' => 'Per tab: poster image path and YouTube video ID (not the full youtube.com URL). Tab labels are edited in the block JSON if needed.',
        'oculus.tab_n' => 'Tab {n}',
        'oculus.poster' => 'Poster path',
        'oculus.youtube_id' => 'YouTube ID',

        'assistant.legend'       => 'Product tabs (blocks 1.7–1.8)',
        'assistant.video_yt'     => 'YouTube ID or URL for video slide',
        'assistant.video_poster' => 'Video slide poster (shown before Play)',
        'assistant.video_label'  => 'Text above Play button',
        'assistant.card_img_0'   => 'Card 1 — image (URL or path)',
        'assistant.card_img_1'   => 'Card 2 — image (URL or path)',
        'assistant.grid_icons'   => 'AI slide icons (4 cells)',
        'assistant.grid_icon_0'  => 'AI Suite — icon',
        'assistant.grid_icon_1'  => 'Clinical Assistant — icon',
        'assistant.grid_icon_2'  => 'Training Coach — icon',
        'assistant.grid_icon_3'  => 'Teamwork Mentor — icon',
        'assistant.tabs' => 'Tab labels (one per line)',
        'assistant.active' => 'Active tab index (0 = first)',
        'assistant.title' => 'Title (first tab)',
        'assistant.lead' => 'Lead paragraph',
        'assistant.p2' => 'Paragraph 2',
        'assistant.p3' => 'Emphasis paragraph',
        'assistant.sidebar' => 'Right column text',
        'assistant.features_hint' => 'Left list',
        'assistant.feature_ph' => 'Add item',
        'assistant.bottom_title' => 'Bottom title',
        'assistant.link_label' => 'Link — label',
        'assistant.link_href' => 'Link — href',

        'vfreeze.legend' => 'Video freeze (block 1-6)',
        'vfreeze.hint'   => 'Video plays automatically and freezes on the last frame. Upload via Media Library and paste the path here.',
        'vfreeze.heading'  => 'Heading (line 1)',
        'vfreeze.heading2' => 'Heading (line 2)',
        'vfreeze.intro'    => 'Intro paragraph',
        'vfreeze.youtube'  => 'YouTube ID or URL (takes priority over MP4)',
        'vfreeze.mp4'      => 'Video MP4 URL (if not YouTube)',
        'vfreeze.poster'   => 'Poster path (shown before playback)',
        'vfreeze.caption'  => 'Caption below video',

        'closing.legend' => 'Closing block',
        'closing.line1' => 'Line 1',
        'closing.line2' => 'Line 2',

        'json.legend' => 'All blocks JSON (optional)',
        'json.home' => 'Home — blocks[]',
        'json.pro' => 'Professionals — blocks[]',
        'json.inst' => 'Institutions — blocks[]',
        'json.blog' => 'Blog — blocks[]',
        'json.partners' => 'Partners — blocks[]',

        'btn.save' => 'Save',
        'btn.login' => 'Sign in',
        'btn.upload' => 'Upload',

        'login.heading' => 'Site admin',
        'login.password' => 'Password',
        'login.to_site' => 'Back to site',
        'login.err_no_hash' => 'Set password_hash in config.local.php (see config.local.example.php).',
        'login.err_bad_password' => 'Incorrect password.',
        'login.hint_config' => 'Create config.local.php from the example and add a password hash:',
        'login.code_hint' => 'php -r "echo password_hash(\'your_password\', PASSWORD_DEFAULT);"',

        'media.heading' => 'Media library',
        'media.upload_legend' => 'Upload image or video',
        'media.upload_hint' => 'JPEG, PNG, WebP, GIF up to ~100 MB; MP4, WebM video. Files go to public/uploads/YEAR/MONTH/; the DB stores path and metadata.',
        'media.file' => 'File',
        'media.no_db' => 'The media library needs SQLite (pdo_sqlite) and data/site.sqlite. Uploads are disabled right now.',
        'media.list_legend' => 'Uploaded files (copy URL into blocks)',
        'media.empty' => 'Nothing here yet.',
        'media.th_id' => 'ID',
        'media.th_preview' => 'Preview / type',
        'media.th_url' => 'URL',
        'media.th_size' => 'Size',
        'media.th_name' => 'Original name',

        'flash.saved_sqlite' => 'Saved (SQLite + site.json backup).',
        'flash.saved_json' => 'Saved (data/site.json).',
        'flash.save_error' => 'Write failed — check permissions on data/ and data/site.sqlite.',
        'flash.media_csrf' => 'CSRF error.',
        'flash.media_no_file' => 'No file uploaded.',
        'flash.media_fail' => 'Could not save file (type, size, or database).',
        'flash.media_ok' => 'Uploaded. URL: {path}',

        'disclosure.toggle' => 'Expand or collapse section',
    ];
}

function admin_page_label(string $slug): string
{
    $key = 'page.' . $slug;
    $t = admin_t($key);
    if ($t !== $key) {
        return $t;
    }

    return $slug;
}

/**
 * Resolve flash message: translation key or legacy plain text.
 */
function admin_flash_text(string $flash): string
{
    $t = admin_t($flash);
    if ($t !== $flash) {
        return $t;
    }

    return $flash;
}

/**
 * @param mixed $flash string (legacy) or ['i18n' => key, 'vars' => [...]]
 */
function admin_render_flash(mixed $flash): string
{
    if (is_array($flash) && isset($flash['i18n']) && is_string($flash['i18n'])) {
        $vars = is_array($flash['vars'] ?? null) ? $flash['vars'] : [];

        return admin_t($flash['i18n'], $vars);
    }
    if (is_string($flash) && $flash !== '') {
        return admin_flash_text($flash);
    }

    return '';
}
