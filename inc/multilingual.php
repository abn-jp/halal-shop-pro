<?php
/**
 * Halal Shop Pro — Multilingual Engine
 *
 * Handles:
 *  - Polylang string registration (theme_mod, static strings)
 *  - Language-aware get_theme_mod() wrapper
 *  - Railway / localhost URL normalization
 *  - WooCommerce multilingual URL fixes
 *  - hreflang SEO tags
 *  - Flatsome theme compatibility
 *  - RTL / body class / locale handling
 *  - Cookie-based fallback locale (no plugin)
 *
 * @package Halal_Shop_Pro
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ═══════════════════════════════════════════════════════════════════════════════
// 1. LANGUAGE DEFINITIONS (single source of truth)
// ═══════════════════════════════════════════════════════════════════════════════

define( 'HALAL_LANGS', [
    'ja' => [ 'name' => '日本語',   'locale' => 'ja',    'flag' => '🇯🇵', 'rtl' => false ],
    'en' => [ 'name' => 'English',  'locale' => 'en_US', 'flag' => '🇬🇧', 'rtl' => false ],
    'id' => [ 'name' => 'Indonesia','locale' => 'id_ID', 'flag' => '🇮🇩', 'rtl' => false ],
    'ar' => [ 'name' => 'العربية',  'locale' => 'ar',    'flag' => '🇸🇦', 'rtl' => true  ],
    'ms' => [ 'name' => 'Melayu',   'locale' => 'ms_MY', 'flag' => '🇲🇾', 'rtl' => false ],
] );

// ═══════════════════════════════════════════════════════════════════════════════
// 2. CURRENT LANGUAGE DETECTION
// ═══════════════════════════════════════════════════════════════════════════════

/**
 * Get active 2-char language slug from any available source.
 * Priority: Polylang → WPML → cookie/query-string fallback.
 */
function halal_lang(): string {
    // Polylang
    if ( function_exists( 'pll_current_language' ) ) {
        $lang = pll_current_language( 'slug' );
        if ( $lang ) return $lang;
    }
    // WPML
    if ( defined( 'ICL_LANGUAGE_CODE' ) && ICL_LANGUAGE_CODE ) {
        return ICL_LANGUAGE_CODE;
    }
    // Cookie / query-string fallback
    return halal_shop_get_fallback_lang();
}

// ═══════════════════════════════════════════════════════════════════════════════
// 3. LANGUAGE-AWARE THEME_MOD WRAPPER
// ═══════════════════════════════════════════════════════════════════════════════

/**
 * Like get_theme_mod() but returns the translated value for the active language.
 *
 * Priority:
 *  1. Inline translation table (works without Polylang String Translations setup)
 *  2. Polylang String Translations pll__()
 *  3. Raw get_theme_mod() / $default
 *
 * Usage in templates: halal_mod( 'hero_title', 'Default text' )
 */
function halal_mod( string $key, string $default = '' ): string {
    $lang = halal_lang();
    $base = get_theme_mod( $key, $default );

    // 1. Inline translation table — covers all hero/customizer strings without
    //    requiring manual entry in Polylang → Languages → String Translations.
    static $inline = null;
    if ( $inline === null ) {
        $inline = [
            'hero_title' => [
                'en' => "Safe & Trusted Halal Food\nOnline Shop",
                'id' => "Toko Online Makanan Halal\nTerpercaya & Aman",
                'ar' => "متجر إلكتروني للطعام الحلال\nآمن وموثوق",
                'ms' => "Kedai Dalam Talian Makanan Halal\nSelamat & Dipercayai",
            ],
            'hero_subtitle' => [
                'en' => 'Delivering Muslim-friendly food nationwide. Curated Halal certified products for Muslim residents and visitors in Japan.',
                'id' => 'Mengirimkan makanan ramah Muslim ke seluruh negeri. Produk bersertifikat Halal pilihan untuk penduduk dan pengunjung Muslim di Jepang.',
                'ar' => 'توصيل الطعام الصديق للمسلمين في جميع أنحاء البلاد. منتجات حلال معتمدة ومختارة بعناية للمقيمين والزوار المسلمين في اليابان.',
                'ms' => 'Menghantar makanan mesra Muslim ke seluruh negara. Produk Halal bersijil pilihan untuk penduduk dan pelawat Muslim di Jepun.',
            ],
            'announcement_text' => [
                'en' => '🎉 Free Shipping on orders over ¥5,000 | Halal Certified Products',
                'id' => '🎉 Gratis Ongkir untuk pembelian di atas ¥5,000 | Produk Bersertifikat Halal',
                'ar' => '🎉 شحن مجاني للطلبات التي تتجاوز ¥5,000 | منتجات معتمدة حلال',
                'ms' => '🎉 Penghantaran Percuma untuk pembelian melebihi ¥5,000 | Produk Bersijil Halal',
            ],
            'footer_about_text' => [
                'en' => 'Japan\'s trusted Halal food online shop. We deliver certified Halal products to Muslim residents and visitors nationwide.',
                'id' => 'Toko online makanan Halal terpercaya di Jepang. Kami mengantarkan produk Halal bersertifikat ke seluruh negeri.',
                'ar' => 'متجر الطعام الحلال الموثوق في اليابان. نوصل المنتجات الحلال المعتمدة للمسلمين في جميع أنحاء البلاد.',
                'ms' => 'Kedai dalam talian makanan Halal yang dipercayai di Jepun. Kami menghantar produk Halal bersijil ke seluruh negara.',
            ],
        ];
    }

    if ( $lang !== 'ja' && isset( $inline[ $key ][ $lang ] ) ) {
        return $inline[ $key ][ $lang ];
    }

    // 2. Polylang String Translations (requires manual setup in WP Admin)
    if ( function_exists( 'pll__' ) && $base ) {
        $translated = pll__( $base );
        if ( $translated && $translated !== $base ) return $translated;
    }

    // 3. Raw theme_mod or default
    return $base !== '' ? $base : $default;
}

// ── theme_mod filters — translate customizer values at get_theme_mod() level ──
// This ensures any direct get_theme_mod('hero_title') call also gets translated.
add_filter( 'theme_mod_hero_title', function( $val ) {
    $lang = halal_lang();
    $t = [
        'en' => "Safe & Trusted Halal Food\nOnline Shop",
        'id' => "Toko Online Makanan Halal\nTerpercaya & Aman",
        'ar' => "متجر إلكتروني للطعام الحلال\nآمن وموثوق",
        'ms' => "Kedai Dalam Talian Makanan Halal\nSelamat & Dipercayai",
    ];
    return $t[ $lang ] ?? $val;
} );

add_filter( 'theme_mod_hero_subtitle', function( $val ) {
    $lang = halal_lang();
    $t = [
        'en' => 'Delivering Muslim-friendly food nationwide. Curated Halal certified products for Muslim residents and visitors in Japan.',
        'id' => 'Mengirimkan makanan ramah Muslim ke seluruh negeri. Produk bersertifikat Halal pilihan untuk penduduk dan pengunjung Muslim di Jepang.',
        'ar' => 'توصيل الطعام الصديق للمسلمين في جميع أنحاء البلاد. منتجات حلال معتمدة ومختارة بعناية للمقيمين والزوار المسلمين في اليابان.',
        'ms' => 'Menghantar makanan mesra Muslim ke seluruh negara. Produk Halal bersijil pilihan untuk penduduk dan pelawat Muslim di Jepun.',
    ];
    return $t[ $lang ] ?? $val;
} );

// ═══════════════════════════════════════════════════════════════════════════════
// 4. POLYLANG STRING REGISTRATION
//    Registers every user-visible theme string so it appears in
//    WP Admin → Languages → String Translations for manual translation.
// ═══════════════════════════════════════════════════════════════════════════════

add_action( 'init', 'halal_pll_register_strings', 20 );
function halal_pll_register_strings(): void {
    if ( ! function_exists( 'pll_register_string' ) ) return;

    $group = 'Halal Shop Pro';

    // ── Customizer / theme_mod strings ────────────────────────────────────────
    $mods = [
        'announcement_text'  => get_theme_mod( 'announcement_text',  '🎉 全国送料無料 ¥5,000以上 | Free Shipping on orders over ¥5,000' ),
        'hero_title'         => get_theme_mod( 'hero_title',          "ハラールフードの\n安心・安全な\nオンラインショップ" ),
        'hero_subtitle'      => get_theme_mod( 'hero_subtitle',       'ムスリムフレンドリーな食品を全国にお届け。厳選されたハラール認証食品を取り揃えています。' ),
        'footer_about_text'  => get_theme_mod( 'footer_about_text',   '' ),
        'footer_copyright'   => get_theme_mod( 'footer_copyright',    '' ),
    ];

    foreach ( $mods as $key => $value ) {
        if ( $value ) {
            pll_register_string( $key, $value, $group, true /* multiline */ );
        }
    }

    // ── Static UI strings ─────────────────────────────────────────────────────
    $strings = [
        'shop_now'              => '商品を見る / Shop Now',
        'halal_certified_badge' => 'ハラール認証取得 | Halal Certified',
        'free_shipping_notice'  => '🎉 全国送料無料 ¥5,000以上 | Free Shipping on orders over ¥5,000',
        'tax_note'              => '※ 消費税10%を含みます / Includes 10% Japanese Consumption Tax',
        'shipping_notice'       => '🚚 全国配送対応（ヤマト運輸・佐川急便） | Nationwide delivery via Yamato & Sagawa',
        'customer_reviews'      => 'お客様の声 / Customer Reviews',
        'read_all_reviews'      => 'すべてのレビューを見る / Read All Reviews',
        'added_to_cart'         => 'カートに追加しました / Added to cart!',
        'view_cart'             => 'カートを見る / View Cart',
        'out_of_stock'          => '在庫切る / Out of Stock',
        'subscribe_thanks'      => 'ご登録ありがとうございます / Thank you for subscribing!',
        'halal_info_title'      => 'ハラールとは？ / What is Halal?',
        'hero_cta_cert'         => 'Halal認証とは？',
        'newsletter_title'      => 'ニュースレター登録 / Subscribe to Newsletter',
    ];

    foreach ( $strings as $key => $value ) {
        pll_register_string( $key, $value, $group );
    }
}

// ═══════════════════════════════════════════════════════════════════════════════
// 5. RAILWAY & LOCALHOST URL NORMALIZATION
//    WordPress stores siteurl/home in the DB. On Railway, if the DB still
//    has localhost values, all URLs break. This filter fixes it at runtime.
// ═══════════════════════════════════════════════════════════════════════════════

add_filter( 'option_siteurl', 'halal_normalize_url' );
add_filter( 'option_home',    'halal_normalize_url' );

function halal_normalize_url( string $url ): string {
    // Detect Railway environment via env vars set in railway.json / service vars
    $railway_host = getenv( 'RAILWAY_PUBLIC_DOMAIN' )    // set by Railway automatically
                 ?: getenv( 'RAILWAY_STATIC_URL' )
                 ?: '';

    if ( $railway_host ) {
        // Force HTTPS on Railway
     2  $url = preg_replace( '#^https?://(localhost|127\.0\.0\.1)(:\d+)?(/[^?]*)?#', 'https://' . rtrim( $railway_host, '/' ) . '$3', $url );
    }

    // If behind a reverse proxy (Railway / Cloudflare) and arriving via HTTPS,
    // ensure siteurl doesn't start with http:// which causes redirect loops.
    if (
        ! empty( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) &&
        $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' &&
        strpos( $url, 'http://' ) === 0
    ) {
        $url = 'https://' . substr( $url, 7 );
    }

    return $url;
}

// Trust X-Forwarded-Proto on Railway (needed for is_ssl() to return true)
add_action( 'init', function () {
    if (
        ! empty( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) &&
        $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'
    ) {
        $_SERVER['HTTPS'] = 'on';
    }
} );

// ═══════════════════════════════════════════════════════════════════════════════
// 6. WOOCOMMERCE MULTILINGUAL FIXES
// ═══════════════════════════════════════════════════════════════════════════════

add_action( 'init', 'halal_woocommerce_multilingual_init' );
function halal_woocommerce_multilingual_init(): void {
    if ( ! class_exists( 'WooCommerce' ) ) return;

    // ── A. Shop page: load translated version ────────────────────────────────
    // Polylang handles this automatically via pll_get_post(), but we ensure
    // WooCommerce page IDs resolve to the translated page for active language.
    add_filter( 'woocommerce_get_page_id', 'halal_translate_wc_page_id', 10, 2 );

    // ── B. Cart/checkout fragments: include language in AJAX key ─────────────
    add_filter( 'woocommerce_cart_hash', function( $hash ) {
        return $hash . '_' . halal_lang();
    } );
}

function halal_translate_wc_page_id( $page_id, $page ) {
    if ( ! function_exists( 'pll_get_post' ) ) return $page_id;
    $translated = pll_get_post( $page_id, pll_current_language() );
    return $translated ?: $page_id;
}

// ── C. WooCommerce email: use customer language, not admin language ──────────
add_filter( 'woocommerce_email_setup_locale', '__return_false' );

// ── D. Currency stays the same across languages (¥ for this store) ──────────
// If you need per-language currency, install "Currency Switcher for WooCommerce"

// ═══════════════════════════════════════════════════════════════════════════════
// 7. HREFLANG SEO TAGS
//    Tells search engines which URL serves which language.
// ═══════════════════════════════════════════════════════════════════════════════

add_action( 'wp_head', 'halal_hreflang_tags', 1 );
function halal_hreflang_tags(): void {
    // Polylang outputs its own hreflang — don't duplicate
    if ( function_exists( 'pll_current_language' ) ) return;
    // WPML also handles this
    if ( function_exists( 'icl_get_languages' ) ) return;

    // Fallback: output basic hreflang for cookie-based switcher
    $current_url = ( is_ssl() ? 'https' : 'http' ) . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $base_url    = preg_replace( '/[?&]lang=[a-z]{2}/', '', $current_url );

    foreach ( HALAL_LANGS as $code => $info ) {
        $url = add_query_arg( 'lang', $code, $base_url );
        echo '<link rel="alternate" hreflang="' . esc_attr( $code ) . '" href="' . esc_url( $url ) . '">' . "\n";
    }
    echo '<link rel="alternate" hreflang="x-default" href="' . esc_url( $base_url ) . '">' . "\n";
}

// ═══════════════════════════════════════════════════════════════════════════════
// 8. BODY CLASS & HTML DIR ATTRIBUTE
// ═══════════════════════════════════════════════════════════════════════════════

add_filter( 'body_class', 'halal_lang_body_classes' );
function halal_lang_body_classes( array $classes ): array {
    $lang = halal_lang();
    $classes[] = 'lang-' . sanitize_html_class( $lang );

    $info = HALAL_LANGS[ $lang ] ?? null;
    if ( $info && $info['rtl'] ) {
        $classes[] = 'rtl';
    }

    return $classes;
}

// ── HTML dir attribute (required for proper RTL rendering) ──────────────────
add_filter( 'language_attributes', 'halal_html_dir_attribute' );
function halal_html_dir_attribute( string $output ): string {
    $lang = halal_lang();
    $info = HALAL_LANGS[ $lang ] ?? null;

    if ( $info ) {
        // Remove any existing dir attribute, add correct one
        $output = preg_replace( '/\s*dir="[^"]*"/', '', $output );
        $output .= ' dir="' . ( $info['rtl'] ? 'rtl' : 'ltr' ) . '"';
        // Ensure lang attribute matches
        $output = preg_replace( '/\s*lang="[^"]*"/', '', $output );
        $output .= ' lang="' . esc_attr( $lang ) . '"';
    }

    return $output;
}

// ═══════════════════════════════════════════════════════════════════════════════
// 9. FLATSOME THEME COMPATIBILITY
//    Flatsome has its own language switcher widget and caches layout.
//    These hooks prevent conflicts.
// ═══════════════════════════════════════════════════════════════════════════════

add_action( 'after_setup_theme', 'halal_flatsome_compat', 100 );
function halal_flatsome_compat(): void {
    // Only run when Flatsome is the active (parent) theme
    $theme = wp_get_theme();
    $is_flatsome = ( $theme->get_template() === 'flatsome' || $theme->get( 'Name' ) === 'Flatsome' );
    if ( ! $is_flatsome ) return;

    // 1. Tell Flatsome which language is active (it reads this for RTL CSS)
    if ( ! defined( 'UX_LANG' ) ) {
        define( 'UX_LANG', halal_lang() );
    }

    // 2. Disable Flatsome's built-in page-level cache when a lang cookie is set
    //    (Flatsome's "Page Cache" caches per-URL, so /en/ and /ja/ are fine,
    //     but the cookie-based fallback needs cache bypass.)
    if ( isset( $_COOKIE['halal_lang'] ) || isset( $_GET['lang'] ) ) {
        if ( ! defined( 'DONOTCACHEPAGE' ) ) {
            define( 'DONOTCACHEPAGE', true );
        }
    }

    // 3. If Flatsome outputs its own language switcher, hide it and use ours
    add_filter( 'flatsome_show_language_switcher', '__return_false' );
}

// ── Disable ALL caching plugins when language is being switched ──────────────
add_action( 'init', 'halal_disable_cache_on_lang_switch' );
function halal_disable_cache_on_lang_switch(): void {
    // Only if we are handling a lang switch (query string present)
    if ( empty( $_GET['lang'] ) && empty( $_COOKIE['halal_lang'] ) ) return;

    // WP Super Cache
    if ( ! defined( 'DONOTCACHEPAGE' ) )   define( 'DONOTCACHEPAGE', true );
    if ( ! defined( 'DONOTCACHEDB' ) )     define( 'DONOTCACHEDB', true );
    if ( ! defined( 'DONOTMINIFY' ) )      define( 'DONOTMINIFY', true );

    // W3 Total Cache
    if ( ! defined( 'DONOTCACHEOBJECT' ) ) define( 'DONOTCACHEOBJECT', true );

    // LiteSpeed Cache
    do_action( 'litespeed_purge_all' );
}

// ═══════════════════════════════════════════════════════════════════════════════
// 10. NAV MENU LANGUAGE FILTER
//     When Polylang is active, menus are auto-filtered per language.
//     When using the cookie fallback, we still want menu items filtered
//     if they have a custom field "lang" set.
// ═══════════════════════════════════════════════════════════════════════════════

add_filter( 'wp_nav_menu_objects', 'halal_filter_menu_by_language', 10, 2 );
function halal_filter_menu_by_language( array $items, object $args ): array {
    // Polylang handles this natively — skip
    if ( function_exists( 'pll_current_language' ) ) return $items;

    $lang = halal_lang();

    return array_filter( $items, function( $item ) use ( $lang ) {
        $item_lang = get_post_meta( $item->ID, '_menu_item_lang', true );
        // If no lang meta, show in all languages
        if ( ! $item_lang ) return true;
        return $item_lang === $lang;
    } );
}

// ═══════════════════════════════════════════════════════════════════════════════
// 11. POLYLANG: ENSURE MISSING TRANSLATIONS REDIRECT TO DEFAULT
//     When a page has no translation for a language, Polylang by default
//     hides the link. We redirect to the default language instead.
// ═══════════════════════════════════════════════════════════════════════════════

add_filter( 'pll_the_language_link', 'halal_pll_fallback_link', 10, 2 );
function halal_pll_fallback_link( $url, $lang ) {
    if ( $url ) return $url;
    // No translation — link to the homepage in that language
    if ( function_exists( 'pll_home_url' ) ) {
        return pll_home_url( $lang );
    }
    return $url;
}

// ═══════════════════════════════════════════════════════════════════════════════
// 12. JAVASCRIPT: PASS LANGUAGE DATA TO FRONTEND
// ═══════════════════════════════════════════════════════════════════════════════

add_action( 'wp_enqueue_scripts', 'halal_localize_lang_data', 20 );
function halal_localize_lang_data(): void {
    if ( ! wp_script_is( 'halal-shop-main', 'enqueued' ) ) return;

    $lang   = halal_lang();
    $langs  = HALAL_LANGS;
    $pll_ok = function_exists( 'pll_current_language' );

    wp_localize_script( 'halal-shop-main', 'halalLang', [
        'current'   => $lang,
        'isRtl'     => isset( $langs[ $lang ] ) && $langs[ $lang ]['rtl'] ? true : false,
        'polylang'  => $pll_ok,
        'homeUrl'   => function_exists( 'pll_home_url' ) ? pll_home_url( $lang ) : home_url( '/' ),
        'switchUrl' => home_url( '/?lang=' ), // for cookie fallback only
    ] );
}

// ═══════════════════════════════════════════════════════════════════════════════
// 13. INLINE TRANSLATION STRINGS (fallback when no .mo files exist)
//     This provides translations for __() / _e() calls in the theme
//     without needing compiled .mo files, using WordPress's gettext filter.
// ═══════════════════════════════════════════════════════════════════════════════

add_filter( 'gettext',        'halal_inline_translations', 10, 3 );
add_filter( 'gettext_with_context', 'halal_inline_translations', 10, 3 );

function halal_inline_translations( string $translation, string $text, string $domain ): string {
    if ( $domain !== 'halal-shop-pro' ) return $translation;
    // Only apply when no .mo file loaded it (translation == original text)
    if ( $translation !== $text ) return $translation;

    static $map = null;
    if ( $map === null ) {
        $map = halal_get_translation_map();
    }

    $lang = halal_lang();
    if ( $lang === 'ja' ) return $translation; // Japanese is the source language

    return $map[ $lang ][ $text ] ?? $translation;
}

/**
 * Translation map: source (Japanese/mixed) → target language.
 * Add or expand entries here for aach new string in the theme.
 */
function halal_get_translation_map(): array {
    return [

        // ────────────────────────────────────────────────────────────────────
        'en' => [
            // Hero
            'ハラール認証取得 | Halal Certified'                                   => 'Halal Certified ✓',
            '商品を見る  / Shop Now'                                                => 'Shop Now',
            'Halal認証とは？'                                                     => 'What is Halal?',
            'ハラール商品'                                                         => 'Halal Products',
            '顧客数'                                                              => 'Customers',
            '対応言語'                                                            => 'Languages',
            '配送対応'                                                            => 'Delivery',
            '翌日'                                                                => 'Next Day',

            // Header / Nav
            'Select Language'                                                     => 'Select Language',
            'Shopping Cart'                                                       => 'Shopping Cart',
            'Close cart'                                                          => 'Close cart',
            'Open cart'                                                           => 'Open cart',
            'Your Cart'                                                           => 'Your Cart',
            'Qty:'                                                                => 'Qty:',
            'View Cart'                                                           => 'View Cart',
            'Checkout'                                                            => 'Checkout',
            'Your cart is empty.'                                                 => 'Your cart is empty.',
            'Total'                                                               => 'Total',
            'Wishlist'                                                            => 'Wishlist',
            'My Account'                                                          => 'My Account',
            'Account'                                                             => 'Account',
            'Login'                                                               => 'Login',
            'Menu'                                                                => 'Menu',
            'Close menu'                                                          => 'Close menu',
            'Open menu'                                                           => 'Open menu',
            'Cart'                                                                => 'Cart',
            'Mobile Navigation'                                                   => 'Navigation',
            'Primary Navigation'                                                  => 'Navigation',
            'Hero Banner'                                                         => 'Hero Banner',

            // Announcement
            '🎉 全国送料無料 ¥5,000以上 | Free Shipping on orders over ¥5,000'   => '🎉 Free Shipping on orders over ¥5,000',

            // Testimonials
            'お客様の声 / Customer Reviews'                                       => 'Customer Reviews',
            'What our Muslim customers around the world say'                      => 'What our Muslim customers around the world say',
            '東京在住 / Pakistani'                                                => 'Tokyo / Pakistani',
            '大阪在住 / Indonesian'                                               => 'Osaka / Indonesian',
            '訪日観光客 / Saudi Arabia'                                           => 'Visitor / Saudi Arabia',
            'すべてのレビューを見る / Read All Reviews'                           => 'Read All Reviews',

            // WooCommerce
            'Home'                                                                => 'Home',
            '※ 消費税10%を含みます / Includes 10% Japanese Consumption Tax'       => 'Includes 10% Japanese Consumption Tax',
            '🚚 全国配送対応（ヤマド運輸・佐川急便） | Nationwide delivery via Yamato & Sagawa' => '🚚 Nationwide delivery (Yamato / Sagawa)',
            'Added to cart!'                                                      => 'Added to cart!',
            'Out of Stock'                                                        => 'Out of Stock',
            'Thank you for subscribing!'                                          => 'Thank you for subscribing!',

            // Halal Info
            'What is Halal?'                                                      => 'What is Halal?',
        ],

        // ────────────────────────────────────────────────────────────────────
        'id' => [
            // Hero
            'ハラール認証取得 | Halal Certified'                                  => 'Bersertifikat Halal ✓',
            '商品を見る / Shop Now'                                               => 'Belanja Sekarang',
            'Halal認証とは？'                                                     => 'Apa itu Halal?',
            'ハラール商品'                                                         => 'Produk Halal',
            '顧客数'                                                              => 'Pelanggan',
            '対応言語'                                                            => 'Bahasa',
            '配送対応'                                                            => 'Pengiriman',
            '翌日'                                                                => 'Besok',

            // Header
            'Shopping Cart'                                                       => 'Keranjang Belanja',
            'Close cart'                                                          => 'Tutup keranjang',
            'Your Cart'                                                           => 'Keranjang Anda',
            'Qty:'                                                                => 'Jml:',
            'View Cart'                                                           => 'Lihat Keranjang',
            'Checkout'                                                            => 'Bayar',
            'Your cart is empty.'                                                 => 'Keranjang Anda kosong.',
            'Total'                                                               => 'Total',
            'My Account'                                                          => 'Akun Saya',
            'Account'                                                             => 'Akun',
            'Login'                                                               => 'Masuk',
            'Cart'                                                                => 'Keranjang',

            // Announcement
            '🎉 全国送料無料 ¥5,000以上 | Free Shipping on orders over ¥5,000'   => '🎉 Gratis Ongkir untuk pembelian di atas ¥5,000',

            // Testimonials
            'お客様の声 / Customer Reviews'                                       => 'Ulasan Pelanggan',
            'What our Muslim customers around the world say'                      => 'Apa kata pelanggan Muslim kami di seluruh dunia',
            '東京在住 / Pakistani'                                                => 'Tokyo / Pakistan',
            '大阪在住 / Indonesian'                                               => 'Osaka / Indonesia',
            '訪日観光客 / Saudi Arabia'                                           => 'Wisatawan / Arab Saudi',
            'すべてのレビューを見る / Read All Reviews'                           => 'Baca Semua Ulasan',

            // WooCommerce
            'Home'                                                                => 'Beranda',
            '※ 消費税10%を含みます / Includes 10% Japanese Consumption Tax'       => 'Sudah termasuk Pajak Konsumsi Jepang 10%',
            '🚚 全国配送対応（ヤマト運輸・佐川急便） | Nationwide delivery via Yamato & Sagawa' => '🚚 Pengiriman ke seluruh Jepang (Yamato / Sagawa)',
            'Added to cart!'                                                      => 'Ditambahkan ke keranjang!',
            'Out of Stock'                                                        => 'Stok Habis',
            'Thank you for subscribing!'                                          => 'Terima kasih telah berlangganan!',
        ],

        // ────────────────────────────────────────────────────────────────────
        'ar' => [
            // Hero
            'ハラール認証取得 | Halal Certified'                                  => 'معتمد حلال ✓',
            '商品を見る / Shop Now'                                               => 'تسوق الآن',
            'Halal認証とは？'                                                     => 'ما هو الحلال؟',
            'ハラール商品'                                                         => 'منتجات حلال',
            '顧客数'                                                              => 'العمقاء',
            '対応言語'                                                            => 'اللغات',
            '配送対応'                                                            => 'التوصيل',
            '翌日'                                                                => 'اليوم التالي',

            // Header
            'Shopping Cart'                                                       => 'سلة التسوق',
            'Close cart'                                                          => 'أغلق السلة',
            'Your Cart'                                                           => 'سلتك',
            'Qty:'                                                                => 'الكمية:',
            'View Cart'                                                           => 'عرض السلة',
            'Checkout'                                                            => 'الدفع',
            'Your cart is empty.'                                                 => 'سلتك فارغة.',
            'Total'                                                               => 'الإجمال',
            'My Account'                                                          => 'حساب',
            'Account'                                                             => 'الحساب',
            'Login'                                                               => 'تسجيل الدخيل',
            'Cart'                                                                => 'السلة',

            // Announcement
            '🎉 全国送料無料 ¥5,000以上 | Free Shipping on orders over ¥5,000'   => '🎉 شحن مجاني للطلبat التي تتجاوز ¥5,000',

            // Testimonials
            'お客様の声 / Customer Reviews'                                       => 'آراء العملاء',
            'What our Muslim customers around the world say'                      => 'ماذا يقول عملاؤنا المسلمون حول العالم',
            'すべてのレビューを見る / Read All Reviews'                           => 'قراءة جميع التقييمات',

            // WooCommerce
            'Home'                                                                => 'الرئيسية',
            '※ 消費税10%を含みます / Includes 10% Japanese Consumption Tax'       => 'يشمل ضريبة الاستهلاك اليابانية 10%',
            '🚚 全国配送対応（ヤマト運輸・佐川急便） | Nationwide delivery via Yamato & Sagawa' => '🚚 توصيل في جميع أنحاء اليابان (Yamato / Sagawa)',
            'Added to cart!'                                                      => 'تمت الإضافة إلى السلة!',
            'Out of Stock'                                                        => 'نفذ المخزون',
            'Thank you for subscribing!'                                          => 'شكراً على اشتراكك!',
        ],

        // ────────────────────────────────────────────────────────────────────
        'ms' => [
            // Hero
            'ハラール認証取得 | Halal Certified'                                  => 'Produk Halal Diperakui ✓',
            '商品を見る / Shop Now'                                               => 'Beli Sekarang',
            'Halal認証とは？'                                                     => 'Apa itu Halal?',
            'ハラール商品'                                                         => 'Produk Halal',
            '顧客数'                                                              => 'Pelanggan',
            '対応言語'                                                            => 'Bahasa',
            '配送対応'                                                            => 'Penghantaran',
            '翌日'                                                                => 'Esok Hari',

            // Header
            'Shopping Cart'                                                       => 'Troli Beli-belah',
            'Close cart'                                                          => 'Tutup troli',
            'Open cart'                                                           => 'Buka troli',
            'Your Cart'                                                           => 'Troli Anda',
            'Qty:'                                                                => 'Kuantiti:',
            'View Cart'                                                           => 'Lihat Troli',
            'Checkout'                                                            => 'Bayar',
            'Your cart is empty.'                                                 => 'Troli anda kosong.',
            'Total'                                                               => 'Jumlah',
            'Wishlist'                                                            => 'Senarai Hajat',
            'My Account'                                                          => 'Akaun Saya',
            'Account'                                                             => 'Akaun',
            'Login'                                                               => 'Log Masuk',
            'Menu'                                                                => 'Menu',
            'Close menu'                                                          => 'Tutup menu',
            'Cart'                                                                => 'Troli',

            // Announcement
            '🎉 全国送料無料 ¥5,000以上 | Free Shipping on orders over ¥5,000'   => '🎉 Penghantaran Percuma untuk pembelian melebihi ¥5,000',

            // Testimonials
            'お客様の声 / Customer Reviews'                                       => 'Ulasan Pelanggan',
            'What our Muslim customers around the world say'                      => 'Apa kata pelanggan Muslim kami di seluruh dunia',
            '東京在住 / Pakistani'                                                => 'Tokyo / Pakistan',
            '大阪在住 / Indonesian'                                               => 'Osaka / Indonesia',
            '訪日観光客 / Saudi Arabia'                                           => 'Pelancong / Arab Saudi',
            'すべてのレビューを見る / Read All Reviews'                           => 'Baca Semua Ulasan',

            // WooCommerce
            'Home'                                                                => 'Laman Utama',
            '※ 消費税10%を含みます / Includes 10% Japanese Consumption Tax'       => 'Sudah termasuk Cukai Penggunaan Jepun 10%',
            '🚚 全国配送対応（ヤマト運輸・佐川急便） | Nationwide delivery via Yamato & Sagawa' => '🚚 Penghantaran ke seluruh Jepun (Yamato / Sagawa)',
            'Added to cart!'                                                      => 'Ditambahkan ke troli!',
            'Out of Stock'                                                        => 'Kehabisan Stok',
            'Thank you for subscribing!'                                          => 'Terima kasih kerana melanggan!',
        ],

    ]; // end return [ 'en'=>..., 'id'=>..., 'ar'=>..., 'ms'=>... ]
}

// ════════════════════════════════════════════════════════════════════════════════
// 14. FALLBACK LANGUAGE DETECTION (cookie / query-string)
//     Used by halal_lang() when no multilingual plugin is active.
// ════════════════════════════════════════════════════════════════════════════════

/**
 * Returns a 2-char language slug from cookie, query-string, or Accept-Language.
 * Sets the cookie when switching via ?lang=XX so future page-loads remember it.
 */
function halal_shop_get_fallback_lang(): string {
    static $cached = null;
    if ( $cached !== null ) return $cached;

    $allowed = array_keys( HALAL_LANGS ); // ['ja','en','id','ar','ms']

    // 1. Query string: ?lang=en  — highest priority, also sets cookie
    if ( ! empty( $_GET['lang'] ) ) {
        $slug = sanitize_key( (string) $_GET['lang'] );
        if ( in_array( $slug, $allowed, true ) ) {
            if ( ! headers_sent() ) {
                setcookie(
                    'halal_lang',
                    $slug,
                    [ 'expires' => time() + 30 * DAY_IN_SECONDS, 'path' => COOKIEPATH, 'domain' => COOKIE_DOMAIN, 'samesite' => 'Lax' ]
                );
            }
            $_COOKIE['halal_lang'] = $slug;
            $cached = $slug;
            return $cached;
        }
    }

    // 2. Cookie set by a previous switch
    if ( ! empty( $_COOKIE['halal_lang'] ) ) {
        $slug = sanitize_key( (string) $_COOKIE['halal_lang'] );
        if ( in_array( $slug, $allowed, true ) ) {
            $cached = $slug;
            return $cached;
        }
    }

    // 3. Browser Accept-Language header (best-effort, first match wins)
    if ( ! empty( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) {
        $accept = strtolower( (string) $_SERVER['HTTP_ACCEPT_LANGUAGE'] );
        foreach ( $allowed as $code ) {
            if ( $code === 'ja' ) continue; // 'ja' is default; skip unless explicit
            if ( strpos( $accept, $code ) !== false ) {
                $cached = $code;
                return $cached;
            }
        }
    }

    // 4. Default: Japanese
    $cached = 'ja';
    return $cached;
}
