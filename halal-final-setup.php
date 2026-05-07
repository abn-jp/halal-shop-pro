<?php
/**
 * Halal Shop Pro — Final Multilingual Setup Script
 *
 * HOW TO USE:
 *   1. This file is run automatically by run-setup.bat
 *   OR manually:
 *   2. Copy this file to C:\laragon\www\halalshop\
 *   3. Open browser: http://localhost/halalshop/halal-final-setup.php
 *   4. This file deletes itself after running.
 *
 * Run from CLI: php halal-final-setup.php
 */

// ─── Detect run mode ─────────────────────────────────────────────────────────
$is_cli = (php_sapi_name() === 'cli');
if (!$is_cli) {
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html><head><meta charset="utf-8">
    <title>Halal Shop — Multilingual Setup</title>
    <style>body{font-family:monospace;background:#1a1a2e;color:#eee;padding:2rem;max-width:900px;margin:0 auto}
    .ok{color:#4ade80}.warn{color:#fbbf24}.err{color:#f87171}.step{color:#38bdf8;font-size:1.1em;margin-top:1.5rem;font-weight:bold}
    pre{background:#0f0f1a;padding:1rem;border-radius:8px;overflow-x:auto}</style></head><body>
    <h1 style="color:#38bdf8">🕌 Halal Shop Pro — Multilingual Setup</h1><pre>';
}

function out($msg, $type='') {
    global $is_cli;
    $prefix = ['ok'=>'  ✓ ','warn'=>'  ⚠ ','err'=>'  ✗ ','step'=>"\n▶ ",''=>'    '];
    if ($is_cli) {
        $colors = ['ok'=>"\033[0;32m",'warn'=>"\033[1;33m",'err'=>"\033[0;31m",'step'=>"\033[1;36m",''=>''];
        $reset = "\033[0m";
        echo $colors[$type] . ($prefix[$type]??'  ') . $msg . $reset . "\n";
    } else {
        $classes = ['ok'=>'ok','warn'=>'warn','err'=>'err','step'=>'step',''=>''];
        $cls = $classes[$type] ?? '';
        echo '<span class="' . $cls . '">' . htmlspecialchars(($prefix[$type]??'  ') . $msg) . '</span>' . "\n";
        ob_flush(); flush();
    }
}

// ─── Bootstrap WordPress ─────────────────────────────────────────────────────
out('Bootstrapping WordPress...', 'step');

// Find WordPress root
$wp_root = '';
$possible_roots = [
    __DIR__,
    dirname(__DIR__),
    'C:/laragon/www/halalshop',
    dirname(__DIR__, 2),
];
foreach ($possible_roots as $dir) {
    if (file_exists($dir . '/wp-load.php') && file_exists($dir . '/wp-config.php')) {
        $wp_root = rtrim($dir, '/\\');
        break;
    }
}

if (!$wp_root) {
    out('WordPress not found! Make sure this file is in the WordPress root folder.', 'err');
    if (!$is_cli) { echo '</pre></body></html>'; } exit(1);
}
out("WordPress root: {$wp_root}", 'ok');

// Set superglobals needed for WP CLI-mode bootstrap
if ($is_cli) {
    $_SERVER['HTTP_HOST']   = 'localhost';
    $_SERVER['REQUEST_URI'] = '/halalshop/';
    $_SERVER['SERVER_NAME'] = 'localhost';
}

define('ABSPATH', $wp_root . '/');
define('WPINC', 'wp-includes');

// Load WordPress
require_once $wp_root . '/wp-load.php';
out('WordPress loaded | Site: ' . get_option('siteurl'), 'ok');

// ─── 1. Verify Polylang ───────────────────────────────────────────────────────
out('Checking Polylang...', 'step');

if (!function_exists('PLL') && !class_exists('Polylang')) {
    // Try to activate it
    $plugin_file = 'polylang/polylang.php';
    if (file_exists(WP_PLUGIN_DIR . '/' . $plugin_file)) {
        activate_plugin($plugin_file);
        out('Polylang activated', 'ok');
        // Reload to get PLL functions
        require_once WP_PLUGIN_DIR . '/polylang/polylang.php';
    } else {
        out('Polylang not installed! Install it from WP Admin → Plugins → Add New → search "polylang"', 'err');
        if (!$is_cli) { echo '</pre></body></html>'; } exit(1);
    }
}

// Make sure Polylang is fully initialized
if (!function_exists('pll_languages_list')) {
    out('Polylang functions not available. Try running from browser (not CLI).', 'warn');
}
out('Polylang is ready', 'ok');

// ─── 2. Add all 5 languages ───────────────────────────────────────────────────
out('Configuring languages (ja, en, id, ar, ms)...', 'step');

$languages = [
    ['name'=>'Japanese',   'slug'=>'ja', 'locale'=>'ja',    'rtl'=>0, 'flag'=>'jp', 'term_group'=>0],
    ['name'=>'English',    'slug'=>'en', 'locale'=>'en_US', 'rtl'=>0, 'flag'=>'gb', 'term_group'=>1],
    ['name'=>'Indonesian', 'slug'=>'id', 'locale'=>'id_ID', 'rtl'=>0, 'flag'=>'id', 'term_group'=>2],
    ['name'=>'Arabic',     'slug'=>'ar', 'locale'=>'ar',    'rtl'=>1, 'flag'=>'sa', 'term_group'=>3],
    ['name'=>'Malay',      'slug'=>'ms', 'locale'=>'ms_MY', 'rtl'=>0, 'flag'=>'my', 'term_group'=>4],
];

if (function_exists('PLL') && PLL()) {
    $existing = pll_languages_list(['fields'=>'slug']) ?: [];
    foreach ($languages as $lang) {
        if (in_array($lang['slug'], $existing, true)) {
            out("Exists: {$lang['name']} ({$lang['slug']})", 'ok');
            continue;
        }
        $result = PLL()->model->add_language($lang);
        if (is_wp_error($result)) {
            out("Failed {$lang['name']}: " . $result->get_error_message(), 'warn');
        } else {
            out("Added: {$lang['name']} ({$lang['slug']})", 'ok');
        }
    }
} else {
    // Direct DB insert as fallback
    out('PLL() not available, inserting languages directly to DB...', 'warn');
    global $wpdb;

    // Polylang stores languages as terms in 'language' taxonomy
    $existing_terms = $wpdb->get_col("SELECT slug FROM {$wpdb->terms} t
        INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id
        WHERE tt.taxonomy = 'language'");

    foreach ($languages as $idx => $lang) {
        if (in_array($lang['slug'], $existing_terms, true)) {
            out("Exists (db): {$lang['name']}", 'ok');
            continue;
        }
        $wpdb->insert($wpdb->terms, ['name'=>$lang['name'], 'slug'=>$lang['slug'], 'term_group'=>$lang['term_group']]);
        $term_id = $wpdb->insert_id;
        if (!$term_id) { out("DB insert failed: {$lang['name']}", 'err'); continue; }
        $wpdb->insert($wpdb->term_taxonomy, [
            'term_id'  => $term_id,
            'taxonomy' => 'language',
            'description' => $lang['locale'],
            'parent'   => 0,
            'count'    => 0,
        ]);
        // Store language meta
        add_term_meta($term_id, 'locale',      $lang['locale'],  true);
        add_term_meta($term_id, 'rtl',         $lang['rtl'],     true);
        add_term_meta($term_id, 'flag_code',   $lang['flag'],    true);
        add_term_meta($term_id, 'active',      1,                true);
        out("Inserted (db): {$lang['name']}", 'ok');
    }
}

// ─── 3. Configure Polylang options ────────────────────────────────────────────
out('Saving Polylang options...', 'step');

$pll_options = get_option('polylang', []);
$pll_options['default_lang']    = 'ja';
$pll_options['force_lang']      = 1;   // URL prefix for all languages
$pll_options['hide_default']    = 0;   // show /ja/ prefix too
$pll_options['browser']         = 1;   // detect from browser Accept-Language
$pll_options['rewrite']         = 1;   // remove /language/ from URL
$pll_options['redirect_lang']   = 1;   // redirect to language home
$pll_options['media_support']   = 0;
$pll_options['uninstall']       = 0;
$pll_options['sync'] = [
    'taxonomies', 'post_format', 'comment_status',
    'ping_status', 'post_parent', 'menu_order',
];
update_option('polylang', $pll_options);
out('Options saved: default=ja, URL-prefix mode enabled', 'ok');

// ─── 4. Permalink structure ────────────────────────────────────────────────────
out('Setting permalink structure...', 'step');
global $wp_rewrite;
$wp_rewrite->set_permalink_structure('/%postname%/');
$wp_rewrite->flush_rules(true);
update_option('rewrite_rules', $wp_rewrite->rewrite_rules());
out('Permalink: /%postname%/ (Polylang adds /lang/ prefix)', 'ok');

// ─── 5. Create translated pages ────────────────────────────────────────────────
out('Creating translated pages...', 'step');

$pages_def = [
    [
        'is_front' => true,
        'template' => '',
        'slugs'    => ['ja'=>'home',              'en'=>'home-en',          'id'=>'home-id',         'ar'=>'home-ar',       'ms'=>'home-ms'],
        'titles'   => ['ja'=>'ホーム',            'en'=>'Home',             'id'=>'Beranda',         'ar'=>'الرئيسية',      'ms'=>'Laman Utama'],
        'contents' => ['ja'=>'',                  'en'=>'',                 'id'=>'',                'ar'=>'',              'ms'=>''],
    ],
    [
        'template' => 'page-templates/template-about.php',
        'slugs'    => ['ja'=>'about',             'en'=>'about-en',         'id'=>'tentang-kami',    'ar'=>'من-نحن',        'ms'=>'tentang-kami-ms'],
        'titles'   => ['ja'=>'会社概要',          'en'=>'About Us',         'id'=>'Tentang Kami',    'ar'=>'من نحن',        'ms'=>'Tentang Kami'],
        'contents' => [
            'ja'=>'<p>会社概要ページです。</p>',
            'en'=>'<p>About Us page.</p>',
            'id'=>'<p>Halaman Tentang Kami.</p>',
            'ar'=>'<p>صفحة من نحن.</p>',
            'ms'=>'<p>Halaman Tentang Kami.</p>',
        ],
    ],
    [
        'template' => 'page-templates/template-faq.php',
        'slugs'    => ['ja'=>'faq',               'en'=>'faq-en',           'id'=>'faq-id',          'ar'=>'الاسئلة-الشائعة','ms'=>'faq-ms'],
        'titles'   => ['ja'=>'よくある質問',      'en'=>'FAQ',              'id'=>'Pertanyaan Umum', 'ar'=>'الأسئلة الشائعة','ms'=>'Soalan Lazim'],
        'contents' => [
            'ja'=>'<p>よくある質問のページです。</p>',
            'en'=>'<p>Frequently Asked Questions.</p>',
            'id'=>'<p>Pertanyaan yang Sering Diajukan.</p>',
            'ar'=>'<p>الأسئلة المتكررة.</p>',
            'ms'=>'<p>Soalan yang Kerap Ditanya.</p>',
        ],
    ],
    [
        'template' => 'page-templates/template-contact.php',
        'slugs'    => ['ja'=>'contact',           'en'=>'contact-en',       'id'=>'kontak',          'ar'=>'اتصل-بنا',      'ms'=>'hubungi-kami'],
        'titles'   => ['ja'=>'お問い合わせ',      'en'=>'Contact Us',       'id'=>'Kontak',          'ar'=>'اتصل بنا',      'ms'=>'Hubungi Kami'],
        'contents' => [
            'ja'=>'<p>お問い合わせはこちらからどうぞ。</p>',
            'en'=>'<p>Get in touch with us.</p>',
            'id'=>'<p>Hubungi kami di sini.</p>',
            'ar'=>'<p>تواصل معنا هنا.</p>',
            'ms'=>'<p>Hubungi kami di sini.</p>',
        ],
    ],
    [
        'template' => 'page-templates/template-halal-cert.php',
        'slugs'    => ['ja'=>'halal-certification','en'=>'halal-cert-en',   'id'=>'sertifikasi-halal','ar'=>'شهادة-حلال',   'ms'=>'pensijilan-halal'],
        'titles'   => ['ja'=>'ハラール認証について','en'=>'Halal Certification','id'=>'Sertifikasi Halal','ar'=>'شهادة الحلال','ms'=>'Pensijilan Halal'],
        'contents' => [
            'ja'=>'<p>ハラール認証についての説明ページです。</p>',
            'en'=>'<p>Information about Halal certification.</p>',
            'id'=>'<p>Informasi tentang sertifikasi Halal.</p>',
            'ar'=>'<p>معلومات حول شهادة الحلال.</p>',
            'ms'=>'<p>Maklumat tentang pensijilan Halal.</p>',
        ],
    ],
];

$active_langs = ['ja','en','id','ar','ms'];
if (function_exists('pll_languages_list')) {
    $fetched = pll_languages_list(['fields'=>'slug']);
    if (!empty($fetched)) $active_langs = $fetched;
}

foreach ($pages_def as $def) {
    $translations = [];
    $is_front = $def['is_front'] ?? false;

    foreach ($active_langs as $lang) {
        if (!isset($def['titles'][$lang])) continue;

        $slug    = $def['slugs'][$lang] ?? (reset($def['slugs']) . '-' . $lang);
        $title   = $def['titles'][$lang];
        $content = $def['contents'][$lang] ?? '';
        $tmpl    = $def['template'] ?? '';

        // Find existing page
        $existing = get_page_by_path($slug, OBJECT, 'page');
        if (!$existing) {
            $found = get_posts(['post_type'=>'page','post_status'=>['publish','draft'],'title'=>$title,'numberposts'=>1]);
            if ($found) {
                $existing = $found[0];
            }
        }

        if ($existing) {
            $page_id = $existing->ID;
            if (function_exists('pll_set_post_language')) {
                pll_set_post_language($page_id, $lang);
            }
            if ($tmpl) update_post_meta($page_id, '_wp_page_template', $tmpl);
            $translations[$lang] = $page_id;
            out("Reused [{$lang}] {$title} (ID:{$page_id})", 'ok');
        } else {
            $page_id = wp_insert_post([
                'post_title'   => $title,
                'post_name'    => $slug,
                'post_content' => $content,
                'post_status'  => 'publish',
                'post_type'    => 'page',
            ], true);

            if (is_wp_error($page_id)) {
                out("Failed [{$lang}] {$title}: " . $page_id->get_error_message(), 'err');
                continue;
            }
            if ($tmpl) update_post_meta($page_id, '_wp_page_template', $tmpl);
            if (function_exists('pll_set_post_language')) {
                pll_set_post_language($page_id, $lang);
            }
            $translations[$lang] = $page_id;
            out("Created [{$lang}] {$title} (ID:{$page_id})", 'ok');
        }
    }

    // Link translations
    if (count($translations) > 1 && function_exists('pll_save_post_translations')) {
        pll_save_post_translations($translations);
        out('Linked: ' . implode(' ↔ ', array_map(fn($l,$id)=>"{$l}#{$id}", array_keys($translations), $translations)), 'ok');
    }

    // Set homepage
    if ($is_front && isset($translations['ja'])) {
        update_option('show_on_front', 'page');
        update_option('page_on_front', $translations['ja']);
        out("Homepage set → ja #{$translations['ja']}", 'ok');
    }
}

// ─── 6. WooCommerce page translations ─────────────────────────────────────────
if (class_exists('WooCommerce')) {
    out('Translating WooCommerce pages...', 'step');
    $wc_pages = [
        'shop'      => ['ja'=>'ショップ',       'en'=>'Shop',       'id'=>'Toko',   'ar'=>'المتجر', 'ms'=>'Kedai'],
        'cart'      => ['ja'=>'カート',         'en'=>'Cart',       'id'=>'Troli',  'ar'=>'السلة',  'ms'=>'Troli'],
        'checkout'  => ['ja'=>'チェックアウト', 'en'=>'Checkout',   'id'=>'Bayar',  'ar'=>'الدفع',  'ms'=>'Daftar Keluar'],
        'myaccount' => ['ja'=>'マイアカウント', 'en'=>'My Account', 'id'=>'Akun',   'ar'=>'حسابي',  'ms'=>'Akaun Saya'],
    ];
    foreach ($wc_pages as $wc_key => $titles) {
        $src_id = (int) get_option("woocommerce_{$wc_key}_page_id");
        if (!$src_id) { out("WC page not found: {$wc_key}", 'warn'); continue; }
        if (function_exists('pll_set_post_language')) {
            pll_set_post_language($src_id, 'ja');
        }
        $tr = function_exists('pll_get_post_translations') ? pll_get_post_translations($src_id) : [];
        $tr['ja'] = $src_id;
        $src_post = get_post($src_id);
        foreach ($active_langs as $lang) {
            if ($lang === 'ja') continue;
            if (isset($tr[$lang]) && get_post($tr[$lang])) {
                out("WC {$wc_key}/{$lang}: exists", 'ok'); continue;
            }
            $new_id = wp_insert_post([
                'post_title'   => $titles[$lang] ?? $src_post->post_title,
                'post_name'    => $src_post->post_name . '-' . $lang,
                'post_content' => $src_post->post_content,
                'post_status'  => 'publish',
                'post_type'    => 'page',
            ], true);
            if (is_wp_error($new_id)) { out("WC {$wc_key}/{$lang} failed: " . $new_id->get_error_message(), 'err'); continue; }
            if (function_exists('pll_set_post_language')) pll_set_post_language($new_id, $lang);
            $tr[$lang] = $new_id;
            out("WC {$wc_key}/{$lang}: created #{$new_id}", 'ok');
        }
        if (function_exists('pll_save_post_translations')) pll_save_post_translations($tr);
        out("WC {$wc_key}: all languages linked", 'ok');
    }
}

// ─── 7. Install Must-Use Plugin ────────────────────────────────────────────────
out('Installing must-use plugin...', 'step');

$mu_dir = WP_CONTENT_DIR . '/mu-plugins';
if (!is_dir($mu_dir)) mkdir($mu_dir, 0755, true);

// mu-plugin source: check theme directory first, then workspace
$theme_dir = get_template_directory();
$mu_src = $theme_dir . '/mu-plugins/halal-lang-fix.php';

// Fallback: write inline if source not found
if (!file_exists($mu_src)) {
    out("mu-plugin source not found at theme dir — writing inline...", 'warn');
    $mu_php = <<<'MUPLUG'
<?php
/**
 * Plugin Name: Halal Shop — Language Fix (Must-Use)
 * Description: Fixes HTTPS/proxy, cache bypass, and language cookie for Halal Shop Pro.
 * Version: 1.0.1
 */
defined('ABSPATH') || exit;

// HTTPS fix for Railway/proxies
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') {
    $_SERVER['HTTPS'] = 'on';
}

// Cache bypass when user has a language cookie
if (isset($_COOKIE['pll_language']) || isset($_COOKIE['halal_lang'])) {
    if (!defined('DONOTCACHEPAGE'))   define('DONOTCACHEPAGE',   true);
    if (!defined('DONOTCACHEDB'))     define('DONOTCACHEDB',     true);
    if (!defined('DONOTMINIFY'))      define('DONOTMINIFY',      true);
    if (!defined('DONOTCACHEOBJECT')) define('DONOTCACHEOBJECT', true);
}

// Fix cookie domain for localhost
add_filter('pll_cookie_domain', function($domain) {
    return (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) ? '' : $domain;
});

// Railway domain override (optional)
if (!empty($_ENV['RAILWAY_PUBLIC_DOMAIN'])) {
    $domain = 'https://' . rtrim($_ENV['RAILWAY_PUBLIC_DOMAIN'], '/');
    add_filter('pre_option_siteurl', fn() => $domain, 1);
    add_filter('pre_option_home',    fn() => $domain, 1);
}
MUPLUG;
    file_put_contents($mu_dir . '/halal-lang-fix.php', $mu_php);
    out('Must-use plugin written inline', 'ok');
} else {
    copy($mu_src, $mu_dir . '/halal-lang-fix.php');
    out('Must-use plugin installed from theme', 'ok');
}

// ─── 8. Register Polylang string translations ──────────────────────────────────
out('Registering theme strings with Polylang...', 'step');

if (function_exists('pll_register_string')) {
    $group = 'Halal Shop Pro';
    $strings = [
        'announcement_text' => get_theme_mod('announcement_text', '🎉 全国送料無料 ¥5,000以上 | Free Shipping on orders over ¥5,000'),
        'hero_title'        => get_theme_mod('hero_title',        "ハラールフードの\n安心・安全な\nオンラインショップ"),
        'hero_subtitle'     => get_theme_mod('hero_subtitle',     'ムスリムフレンドリーな食品を全国にお届け。'),
        'shop_now'          => '商品を見る / Shop Now',
        'halal_badge'       => 'ハラール認証取得 | Halal Certified',
        'customer_reviews'  => 'お客様の声 / Customer Reviews',
        'tax_note'          => '※ 消費税10%を含みます / Includes 10% Japanese Consumption Tax',
    ];
    $n = 0;
    foreach ($strings as $k => $v) {
        if (!$v) continue;
        pll_register_string($k, $v, $group, in_array($k, ['hero_title','hero_subtitle']));
        $n++;
    }
    out("Registered {$n} strings → WP Admin → Languages → String Translations", 'ok');
} else {
    out('pll_register_string not available — strings will register on first site visit', 'warn');
}

// ─── 9. Flush rewrite rules ────────────────────────────────────────────────────
out('Flushing rewrite rules...', 'step');
flush_rewrite_rules(true);
out('Rewrite rules flushed', 'ok');

// ─── 10. Cache clear ──────────────────────────────────────────────────────────
wp_cache_flush();
out('Object cache cleared', 'ok');

// ─── Summary ──────────────────────────────────────────────────────────────────
$site = rtrim(get_option('siteurl'), '/');
$langs_list = function_exists('pll_languages_list') ? implode(', ', pll_languages_list(['fields'=>'slug'])) : 'ja, en, id, ar, ms';

out('', '');
out('══════════════════════════════════════════════', 'step');
out('MULTILINGUAL SETUP COMPLETE!', 'step');
out('══════════════════════════════════════════════', '');
out('Languages: ' . $langs_list, 'ok');
out('', '');
out('Test URLs:', '');
foreach (['ja','en','id','ar','ms'] as $l) {
    out("  {$l}: {$site}/{$l}/", '');
}
out('', '');
out('Next steps:', '');
out('1. WP Admin → Settings → Permalinks → click "Save Changes"', '');
out('2. WP Admin → Languages → String Translations → translate hero texts', '');
out('3. WP Admin → Pages → add real content to each language page', '');
out('4. WP Admin → WooCommerce → Products → add translations for each product', '');

// ─── Self-delete ───────────────────────────────────────────────────────────────
if (!$is_cli) {
    $self = __FILE__;
    register_shutdown_function(function() use ($self) {
        if (file_exists($self)) @unlink($self);
    });
    echo '</pre>';
    echo '<div style="margin-top:2rem;padding:1rem;background:#14532d;border-radius:8px;">
    <strong style="color:#4ade80">✓ Setup complete!</strong> This file will self-delete.<br><br>
    <strong>Test your site:</strong><br>';
    foreach (['ja','en','id','ar','ms'] as $l) {
        echo "<a href=\"{$site}/{$l}/\" style=\"color:#38bdf8;margin-right:1rem;\" target=\"_blank\">{$site}/{$l}/</a> ";
    }
    echo '</div></body></html>';
}
