<?php
/**
 * Halal Shop Pro â Multilingual Engine
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

// âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
// 1. LANGUAGE DEFINITIONS (single source of truth)
// âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ

define( 'HALAL_LANGS', [
    'ja' => [ 'name' => 'æ¥æ¬èª',   'locale' => 'ja',    'flag' => 'ð¯ðµ', 'rtl' => false ],
    'en' => [ 'name' => 'English',  'locale' => 'en_US', 'flag' => 'ð¬ð§', 'rtl' => false ],
    'id' => [ 'name' => 'Indonesia','locale' => 'id_ID', 'flag' => 'ð®ð©', 'rtl' => false ],
    'ar' => [ 'name' => 'Ø§ÙØ¹Ø±Ø¨ÙØ©',  'locale' => 'ar',    'flag' => 'ð¸ð¦', 'rtl' => true  ],
    'ms' => [ 'name' => 'Melayu',   'locale' => 'ms_MY', 'flag' => 'ð²ð¾', 'rtl' => false ],
] );

// âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
// 2. CURRENT LANGUAGE DETECTION
// âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ

/**
 * Get active 2-char language slug from any available source.
 * Priority: Polylang â WPML â cookie/query-string fallback.
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

// âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
// 3. LANGUAGE-AWARE THEME_MOD WRAPPER
// âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ

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

    // 1. Inline translation table â covers all hero/customizer strings without
    //    requiring manual entry in Polylang â Languages â String Translations.
    static $inline = null;
    if ( $inline === null ) {
        $inline = [
            'hero_title' => [
                'en' => "Safe & Trusted Halal Food\nOnline Shop",
                'id' => "Toko Online Makanan Halal\nTerpercaya & Aman",
                'ar' => "ÙØªØ¬Ø± Ø¥ÙÙØªØ±ÙÙÙ ÙÙØ·Ø¹Ø§Ù Ø§ÙØ­ÙØ§Ù\nØ¢ÙÙ ÙÙÙØ«ÙÙ",
                'ms' => "Kedai Dalam Talian Makanan Halal\nSelamat & Dipercayai",
            ],
            'hero_subtitle' => [
                'en' => 'Delivering Muslim-friendly food nationwide. Curated Halal certified products for Muslim residents and visitors in Japan.',
                'id' => 'Mengirimkan makanan ramah Muslim ke seluruh negeri. Produk bersertifikat Halal pilihan untuk penduduk dan pengunjung Muslim di Jepang.',
                'ar' => 'ØªÙØµÙÙ Ø§ÙØ·Ø¹Ø§Ù Ø§ÙØµØ¯ÙÙ ÙÙÙØ³ÙÙÙÙ ÙÙ Ø¬ÙÙØ¹ Ø£ÙØ­Ø§Ø¡ Ø§ÙØ¨ÙØ§Ø¯. ÙÙØªØ¬Ø§Øª Ø­ÙØ§Ù ÙØ¹ØªÙØ¯Ø© ÙÙØ®ØªØ§Ø±Ø© Ø¨Ø¹ÙØ§ÙØ© ÙÙÙÙÙÙÙÙ ÙØ§ÙØ²ÙØ§Ø± Ø§ÙÙØ³ÙÙÙÙ ÙÙ Ø§ÙÙØ§Ø¨Ø§Ù.',
                'ms' => 'Menghantar makanan mesra Muslim ke seluruh negara. Produk Halal bersijil pilihan untuk penduduk dan pelawat Muslim di Jepun.',
            ],
            'announcement_text' => [
                'en' => 'ð Free Shipping on orders over Â¥5,000 | Halal Certified Products',
                'id' => 'ð Gratis Ongkir untuk pembelian di atas Â¥5,000 | Produk Bersertifikat Halal',
                'ar' => 'ð Ø´Ø­Ù ÙØ¬Ø§ÙÙ ÙÙØ·ÙØ¨Ø§Øª Ø§ÙØªÙ ØªØªØ¬Ø§ÙØ² Â¥5,000 | ÙÙØªØ¬Ø§Øª ÙØ¸ØªÙØ¯Ø© Ø­ÙØ§Ù',
                'ms' => 'ð Penghantaran Percuma untuk pembelian melebihi Â¥5,000 | Produk Bersijil Halal',
            ],
            'footer_about_text' => [
                'en' => 'Japan\'s trusted Halal food online shop. We deliver certified Halal products to Muslim residents and visitors nationwide.',
                'id' => 'Toko online makanan Halal terpercaya di Jepang. Kami mengantarkan produk Halal bersertifikat ke seluruh negeri.',
                'ar' => 'ÙØªØ¬Ø± Ø§ÙØ·Ø¹Ø§Ù Ø§ÙØ­ÙØ§Ù Ø§ÙÙÙØ«ÙÙ ÙÙ Ø§ÙÙØ§Ø¨Ø§Ù. ÙÙØµÙ Ø§ÙÙÙØªØ¬Ø§Øª Ø§ÙØ­ÙØ§Ù Ø§ÙÙØ¹ØªÙØ¯Ø© ÙÙÙØ³ÙÙÙÙ ÙÙ Ø¬ÙÙØ¹ Ø£ÙØ­Ø§Ø¡ Ø§ÙØ¨ÙØ§Ø¯.',
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

// ââ theme_mod filters â translate customizer values at get_theme_mod() level ââ
// This ensures any direct get_theme_mod('hero_title') call also gets translated.
add_filter( 'theme_mod_hero_title', function( $val ) {
    $lang = halal_lang();
    $t = [
        'en' => "Safe & Trusted Halal Food\nOnline Shop",
        'id' => "Toko Online Makanan Halal\nTerpercaya & Aman",
        'ar' => "ÙØªØ¬Ø± Ø¥ÙÙØªØ±ÙÙÙ ÙÙØ·Ø¹Ø§Ù Ø§ÙØ­ÙØ§Ù\nØ¢ÙÙ ÙÙÙØ«ÙÙ",
        'ms' => "Kedai Dalam Talian Makanan Halal\nSelamat & Dipercayai",
    ];
    return $t[ $lang ] ?? $val;
} );

add_filter( 'theme_mod_hero_subtitle', function( $val ) {
    $lang = halal_lang();
    $t = [
        'en' => 'Delivering Muslim-friendly food nationwide. Curated Halal certified products for Muslim residents and visitors in Japan.',
        'id' => 'Mengirimkan makanan ramah Muslim ke seluruh negeri. Produk bersertifikat Halal pilihan untuk penduduk dan pengunjung Muslim di Jepang.',
        'ar' => 'ØªÙØµÙÙ Ø§ÙØ·Ø¹Ø§Ù Ø§ÙØµØ¯ÙÙ ÙÙÙØ³ÙÙÙÙ ÙÙ Ø¬ÙÙØ¹ Ø£ÙØ­Ø§Ø¡ Ø§ÙØ¨ÙØ§Ø¯. ÙÙØªØ¬Ø§Øª Ø­ÙØ§Ù ÙØ¹ØªÙØ¯Ø© ÙÙØ®ØªØ§Ø±Ø© Ø¨Ø¹ÙØ§ÙØ© ÙÙÙÙÙÙÙÙ ÙØ§ÙØ²ÙØ§Ø± Ø§ÙÙØ³ÙÙÙÙ ÙÙ Ø§ÙÙØ§Ø¨Ø§Ù.',
        'ms' => 'Menghantar makanan mesra Muslim ke seluruh negara. Produk Halal bersijil pilihan untuk penduduk dan pelawat Muslim di Jepun.',
    ];
    return $t[ $lang ] ?? $val;
} );

// âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
// 4. POLYLANG STRING REGISTRATION
//    Registers every user-visible theme string so it appears in
//    WP Admin â Languages â String Translations for manual translation.
// âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ

add_action( 'init', 'halal_pll_register_strings', 20 );
function halal_pll_register_strings(): void {
    if ( ! function_exists( 'pll_register_string' ) ) return;

    $group = 'Halal Shop Pro';

    // ââ Customizer / theme_mod strings ââââââââââââââââââââââââââââââââââââââââ
    $mods = [
        'announcement_text'  => get_theme_mod( 'announcement_text',  'ð å¨å½éæç¡æ Â¥5,000ä»¥ä¸ | Free Shipping on orders over Â¥5,000' ),
        'hero_title'         => get_theme_mod( 'hero_title',          "ãã©ã¼ã«ãã¼ãã®\nå®å¿ã»å®å¨ãª\nãªã³ã©ã¤ã³ã·ã§ãã" ),
        'hero_subtitle'      => get_theme_mod( 'hero_subtitle',       'ã ã¹ãªã ãã¬ã³ããªã¼ãªé£åãå¨å½ã«ãå±ããå³é¸ããããã©ã¼ã«èªè¨¼é£åãåãæãã¦ãã¾ãã' ),
        'footer_about_text'  => get_theme_mod( 'footer_about_text',   '' ),
        'footer_copyright'   => get_theme_mod( 'footer_copyright',    '' ),
    ];

    foreach ( $mods as $key => $value ) {
        if ( $value ) {
            pll_register_string( $key, $value, $group, true /* multiline */ );
        }
    }

    // ââ Static UI strings âââââââââââââââââââââââââââââââââââââââââââââââââââââ
    $strings = [
        'shop_now'              => 'ååãè¦ã / Shop Now',
        'halal_certified_badge' => 'ãã©ã¼ã«èªè¨¼åå¾ | Halal Certified',
        'free_shipping_notice'  => 'ð å¨å½éæç¡æ Â¥5,000ä»¥ä¸ | Free Shipping on orders over Â¥5,000',
        'tax_note'              => 'â» æ¶è²»ç¨10%ãå«ã¿ã¾ã / Includes 10% Japanese Consumption Tax',
        'shipping_notice'       => 'ð å¨å½ééå¯¾å¿ï¼ã¤ããéè¼¸ã»ä½å·æ¥ä¾¿ï¼ | Nationwide delivery via Yamato & Sagawa',
        'customer_reviews'      => 'ãå®¢æ§ã®å£° / Customer Reviews',
        'read_all_reviews'      => 'ãã¹ã¦ã®ã¬ãã%ã¼ãè¦ã / Read All Reviews',
        'added_to_cart'         => 'ã«ã¼ãã«è¿½å ãã¾ãã / Added to cart!',
        'view_cart'             => 'ã«ã¼ããè¦ã / View Cart',
        'out_of_stock'          => 'å¨åº«åã / Out of Stock',
        'subscribe_thanks'      => 'ãç»é²ãããã¨ããããã¾ã / Thank you for subscribing!',
        'halal_info_title'      => 'ãã©ã¼ã«ã¨ã¯ï¼ / What is Halal?',
        'hero_cta_cert'         => 'Halalèªè¨¼ã¨ã¯ï¼',
        'newsletter_title'      => 'ãã¥ã¼ã¹ã¬ã¿ã¼ç»é² / Subscribe to Newsletter',
    ];

    foreach ( $strings as $key => $value ) {
        pll_register_string( $key, $value, $group );
    }
}

// âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
// 5. RAILWAY & LOCALHOST URL NORMALIZATION
//    WordPress stores siteurl/home in the DB. On Railway, if the DB still
//    has localhost values, all URLs break. This filter fixes it at runtime.
// âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ

add_filter( 'option_siteurl', 'halal_normalize_url' );
add_filter( 'option_home',    'halal_normalize_url' );

function halal_normalize_url( string $url ): string {
    // Detect Railway environment via env vars set in railway.json / service vars
    $railway_host = getenv( 'RAILWAY_PUBLIC_DOMAIN' )    // set by Railway automatically
                 ?: getenv( 'RAILWAY_STATIC_URL' )
                 ?: '';

    if ( $railway_host ) {
        // Force HTTPS on Railway
        $url = preg_replace( '#^https?://(localhost|127\.0\.0\.1)(:\d+)?(/[^?]*)?#', 'https://' . rtrim( $railway_host, '/' ) . '$3', $url );
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

// âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
// 6. WOOCOMMERCE MULTILINGUAL FIXES
// âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ

add_action( 'init', 'halal_woocommerce_multilingual_init' );
function halal_woocommerce_multilingual_init(): void {
    if ( ! class_exists( 'WooCommerce' ) ) return;

    // ââ A. Shop page: load translated version ââââââââââââââââââââââââââââââââ
    // Polylang handles this automatically via pll_get_post(), but we ensure
    // WooCommerce page IDs resolve to the translated page for the active language.
    add_filter( 'woocommerce_get_page_id', 'halal_translate_wc_page_id', 10, 2 );

    // ââ B. Cart/checkout fragments: include language in AJAX key âââââââââââââ
    add_filter( 'woocommerce_cart_hash', function( $hash ) {
        return $hash . '_' . halal_lang();
    } );
}

function halal_translate_wc_page_id( $page_id, $page ) {
    if ( ! function_exists( 'pll_get_post' ) ) return $page_id;
    $translated = pll_get_post( $page_id, pll_current_language() );
    return $translated ?: $page_id;
}

// ââ C. WooCommerce email: use customer language, not admin language ââââââââââ
add_filter( 'woocommerce_email_setup_locale', '__return_false' );

// ââ D. Currency stays the same across languages (Â¥ for this store) ââââââââââ
// If you need per-language currency, install "Currency Switcher for WooCommerce"

// âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
// 7. HREFLANG SEO TAGS
//    Tells search engines which URL serves which language.
// âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ

add_action( 'wp_head', 'halal_hreflang_tags', 1 );
function halal_hreflang_tags(): void {
    // Polylang outputs its own hreflang â don't duplicate
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

// âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
// 8. BODY CLASS & HTML DIR ATTRIBUTE
// âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ

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

// ââ HTML dir attribute (required for proper RTL rendering) ââââââââââââââââââ
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

// âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
// 9. FLATSOME THEME COMPATIBILITY
//    Flatsome has its own language switcher widget and caches layout.
//    These hooks prevent conflicts.
// âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ

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

// ââ Disable ALL caching plugins when language is being switched ââââââââââââââ
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

// âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
// 10. NAV MENU LANGUAGE FILTER
//     When Polylang is active, menus are auto-filtered per language.
//     When using the cookie fallback, we still want menu items filtered
//     if they have a custom field "lang" set.
// âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ

add_filter( 'wp_nav_menu_objects', 'halal_filter_menu_by_language', 10, 2 );
function halal_filter_menu_by_language( array $items, object $args ): array {
    // Polylang handles this natively â skip
    if ( function_exists( 'pll_current_language' ) ) return $items;

    $lang = halal_lang();

    return array_filter( $items, function( $item ) use ( $lang ) {
        $item_lang = get_post_meta( $item->ID, '_menu_item_lang', true );
        // If no lang meta, show in all languages
        if ( ! $item_lang ) return true;
        return $item_lang === $lang;
    } );
}

// âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
// 11. POLYLANG: ENSURE MISSING TRANSLATIONS REDIRECT TO DEFAULT
//     When a page has no translation for a language, Polylang by default
//     hides the link. We redirect to the default language instead.
// âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ

add_filter( 'pll_the_language_link', 'halal_pll_fallback_link', 10, 2 );
function halal_pll_fallback_link( $url, $lang ) {
    if ( $url ) return $url;
    // No translation â link to the homepage in that language
    if ( function_exists( 'pll_home_url' ) ) {
        return pll_home_url( $lang );
    }
    return $url;
}

// âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
// 12. JAVASCRIPT: PASS LANGUAGE DATA TO FRONTEND
// âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ

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

// âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
// 13. INLINE TRANSLATION STRINGS (fallback when no .mo files exist)
//     This provides translations for __() / _e() calls in the theme
//     without needing compiled .mo files, using WordPress's gettext filter.
// âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ

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
 * Translation map: source (Japanese/mixed) â target language.
 * Add or expand entries here for fach new string in the theme.
 */
function halal_get_translation_map(): array {
    return [

        // ââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
        'en' => [
            // Hero
            'ãã©ã¼ã«èªè¨¼åå¾ | Halal Certified'                                  => 'Halal Certified â',
            'ååãè¦ã / Shop Now'                                               => 'Shop Now',
            'Halalèªè¨¼ã¨ã¯ï¼'                                                     => 'What is Halal?',
            'ãã©ã¼ã«åå'                                                         => 'Halal Products',
            'é¡§å®¢æ°'                                                              => 'Customers',
            'å¯¾å¿è¨èª'                                                            => 'Languages',
            'ééå¯¾å¿'                                                            => 'Delivery',
            'ç¿æ¥'                                                                => 'Next Day',

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
            'ð å¨å½éæç¡æ Â¥5,000ä»¥ä¸ | Free Shipping on orders over Â¥5,000'   => 'ð Free Shipping on orders over Â¥5,000',

            // Testimonials
            'ãå®¢æ§ã®å£° / Customer Reviews'                                       => 'Customer Reviews',
            'What our Muslim customers around the world say'                      => 'What our Muslim customers around the world say',
            'æ±äº¬å¨ä½ / Pakistani'                                                => 'Tokyo / Pakistani',
            'å¤§éªå¨ä½ / Indonesian'                                               => 'Osaka / Indonesian',
            'è¨ªæ¥è¦³åå®¢ / Saudi Arabia'                                           => 'Visitor / Saudi Arabia',
            'ãã¹ã¦ã®ã¬ãã¥ã¼ãè¦ã / Read All Reviews'                           => 'Read All Reviews',

            // WooCommerce
            'Home'                                                                => 'Home',
            'â» æ¶è²»ç¨10%ãå«ã¿ã¾ã / Includes 10% Japanese Consumption Tax'       => 'Includes 10% Japanese Consumption Tax',
            'ð å¨å½ééå¯¾å¿ï¼ã¤ããéè¼¸ã»ä½å·æ¥ä¾¿ï¼ | Nationwide delivery via Yamato & Sagawa' => 'ð Nationwide delivery (Yamato / Sagawa)',
            'Added to cart!'                                                      => 'Added to cart!',
            'Out of Stock'                                                        => 'Out of Stock',
            'Thank you for subscribing!'                                          => 'Thank you for subscribing!',

            // Halal Info
            'What is Halal?'                                                      => 'What is Halal?',
        ],

        // ââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
        'id' => [
            // Hero
            'ãã©ã¼ã«èªè¨¼åå¾ | Halal Certified'                                  => 'Bersertifikat Halal â',
            'ååãè¦ã / Shop Now'                                               => 'Belanja Sekarang',
            'Halalèªè¨¼ã¨ã¯ï¼'                                                     => 'Apa itu Halal?',
            'ãã©ã¼ã«åå'                                                         => 'Produk Halal',
            'é¡§å®¢æ°'                                                              => 'Pelanggan',
            'å¯¾å¿è¨èª'                                                            => 'Bahasa',
            'ééå¯¾å¿'                                                            => 'Pengiriman',
            'ç¿æ¥'                                                                => 'Besok',

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
            'ð å¨å½éæç¡æ Â¥5,000ä»¥ä¸ | Free Shipping on orders over Â¥5,000'   => 'ð Gratis Ongkir untuk pembelian di atas Â¥5,000',

            // Testimonials
            'ãå®¢æ§ã®å£° / Customer Reviews'                                       => 'Ulasan Pelanggan',
            'What our Muslim customers around the world say'                      => 'Apa kata pelanggan Muslim kami di seluruh dunia',
            'æ±äº¬å¨ä½ / Pakistani'                                                => 'Tokyo / Pakistan',
            'å¤§éªå¨ä½ / Indonesian'                                               => 'Osaka / Indonesia',
            'è¨ªæ¥è¦³åå®¢ / Saudi Arabia'                                           => 'Wisatawan / Arab Saudi',
            'ãã¹ã¦ã®ã¬ãã¥ã¼ãè¦ã / Read All Reviews'                           => 'Baca Semua Ulasan',

            // WooCommerce
            'Home'                                                                => 'Beranda',
            'â» æ¶è²»ç¨10%ãå«ã¿ã¾ã / Includes 10% Japanese Consumption Tax'       => 'Sudah termasuk Pajak Konsumsi Jepang 10%',
            'ð å¨å½ééå¯¾å¿ï¼ã¤ããéè¼¸ã»ä½å·æ¥ä¾¿ï¼ | Nationwide delivery via Yamato & Sagawa' => 'ð Pengiriman ke seluruh Jepang (Yamato / Sagawa)',
            'Added to cart!'                                                      => 'Ditambahkan ke keranjang!',
            'Out of Stock'                                                        => 'Stok Habis',
            'Thank you for subscribing!'                                          => 'Terima kasih telah berlangganan!',
        ],

        // ââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
        'ar' => [
            // Hero
            'ãã©ã¼ã«èªè¨¼åå¾ | Halal Certified'                                  => 'ÙØ¹ØªÙØ¯ Ø­ÙØ§Ù â',
            'ååãè¦ã / Shop Now'                                               => 'ØªØ³ÙÙ Ø§ÙØ¢Ù',
            'Halalèªè¨¼ã¨ã¯ï¼'                                                     => 'ÙØ§ ÙÙ Ø§ÙØ­ÙØ§ÙØ',
            'ãã©ã¼ã«åå'                                                         => 'ÙÙØªØ¬Ø§Øª Ø­ÙØ§Ù',
            'é¡§å®¢æ°'                                                              => 'Ø§ÙØ¹ÙÙØ§Ø¡',
            'å¯¾å¿è¨èª'                                                            => 'Ø§ÙÙØºØ§Øª',
            'ééå¯¾å¿'                                                            => 'Ø§ÙØªÙØµÙÙ',
            'ç¿æ¥'                                                                => 'Ø§ÙÙÙÙ Ø§ÙØªØ§ÙÙ',

            // Header
            'Shopping Cart'                                                       => 'Ø³ÙØ© Ø§ÙØªØ³ÙÙ',
            'Close cart'                                                          => 'Ø£ØºÙÙ Ø§ÙØ³ÙØ©',
            'Your Cart'                                                           => 'Ø³ÙØªÙ',
            'Qty:'                                                                => 'Ø§ÙÙÙÙØ©:',
            'View Cart'                                                           => 'Ø¹Ø±Ø¶ Ø§ÙØ³ÙØ©',
            'Checkout'                                                            => 'Ø§ÙØ¯ÙØ¹',
            'Your cart is empty.'                                                 => 'Ø³ÙØªÙ ÙØ§Ø±ØºØ©.',
            'Total'                                                               => 'Ø§ÙØ¥Ø¬ÙØ§ÙÙ',
            'My Account'                                                          => 'Ø­Ø³Ø§Ø¨Ù',
            'Account'                                                             => 'Ø§ÙØ­Ø³Ø§Ø¨',
            'Login'                                                               => 'ØªØ³Ø¬ÙÙ Ø§ÙØ¯Ø®ÙÙ',
            'Cart'                                                                => 'Ø§ÙØ³ÙØ©',

            // Announcement
            'ð å¨å½éæç¡æ Â¥5,000ä»¥ä¸ | Free Shipping on orders over Â¥5,000'   => 'ð Ø´Ø­Ù ÙØ¬Ø§ÙÙ ÙÙØ·ÙØ¨Ø§Øª Ø§ÙØªÙ ØªØªØ¬Ø§ÙØ² Â¥5,000',

            // Testimonials
            'ãå®¢æ§ã®å£° / Customer Reviews'                                       => 'Ø¢Ø±Ø§Ø¡ Ø§ÙØ¹ÙÙØ§Ø¡',
            'What our Muslim customers around the world say'                      => 'ÙØ§Ø°Ø§ ÙÙÙÙ Ø¹ÙÙØ§Ø¤ÙØ§ Ø§ÙÙØ³ÙÙÙÙ Ø­ÙÙ Ø§ÙØ¹Ø§ÙÙ',
            'ãã¹ã¦ã®ã¬ãã¥ã¼ãè¦ã / Read All Reviews'                           => 'ÙØ±Ø§Ø¡Ø© Ø¬ÙÙØ¹ Ø§ÙØªÙÙÙÙØ§Øª',

            // WooCommerce
            'Home'                                                                => 'Ø§ÙØ±Ø¦ÙØ³ÙØ©',
            'â» æ¶è²»ç¨10%ãå«ã¿ã¾ã / Includes 10% Japanese Consumption Tax'       => 'ÙØ´ÙÙ Ø¶Ø±ÙØ¨Ø© Ø§ÙØ§Ø³ØªÙÙØ§Ù Ø§ÙÙØ§Ø¨Ø§ÙÙØ© 10%',
            'ð å¨å½ééå¯¾å¿ï¼ã¤ããéè¼¸ã»ä½å·æ¥ä¾¿ï¼ | Nationwide delivery via Yamato & Sagawa' => 'ð ØªÙØµÙÙ ÙÙ Ø¬ÙÙØ¹ Ø£ÙØ­Ø§Ø¡ Ø§ÙÙØ§Ø¨Ø§Ù (Yamato / Sagawa)',
            'Added to cart!'                                                      => 'ØªÙØª Ø§ÙØ¥Ø¶Ø§ÙØ© Ø¥ÙÙ Ø§ÙØ³ÙØ©!',
            'Out of Stock'                                                          => 'ÙÙØ°  Ø§ÙÙØ®Ø²ÙÙ',
            'Thank you for subscribing!'                                          => 'Ø´ÙØ±Ø§Ù Ø¹ÙÙ Ø§Ø´ØªØ±Ø§ÙÙ!',
        ],

        // ââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
        'ms' => [
            // Hero
            'ãã©ã¼ã«èªè¨¼åå¾ | Halal Certified'                                  => 'Produk Halal Diperakui â',
            'ååãè¦ã / Shop Now'                                               => 'Beli Sekarang',
            'Halalèªè¨¼ã¨ã¯ï¼'                                                     => 'Apa itu Halal?',
            'ãã©ã¼ã«åå'                                                         => 'Produk Halal',
            'é¡§å®¢æ°'                                                              => 'Pelanggan',
            'å¯¾å¿è¨èª'                                                            => 'Bahasa',
            'ééå¯¾å¿'                                                            => 'Penghantaran',
            'ç¿æ¥'                                                                => 'Esok Hari',

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
            'ð å¨å½éæç¡æ Â¥5,000ä»¥ä¸ | Free Shipping on orders over Â¥5,000'   => 'ð Penghantaran Percuma untuk pembelian melebihi Â¥5,000',

            // Testimonials
            'ãå®¢æ§ã®å£° / Customer Reviews'                                       => 'Ulasan Pelanggan',
            'What our Muslim customers around the world say'                      => 'Apa kata pelanggan Muslim kami di seluruh dunia',
            'æ±äº¬å¨ä½ / Pakistani'                                                => 'Tokyo / Pakistan',
            'å¤§éªå¨ä½ / Indonesian'                                               => 'Osaka / Indonesia',
            'è¨ªæ¥è¦³åå®¢ / Saudi Arabia'                                           => 'Pelancong / Arab Saudi',
            'ãã¹ã¦ã®ã¬ãã¥ã¼ãè¦ã / Read All Reviews'                           => 'Baca Semua Ulasan',

            // WooCommerce
            'Home'                                                                => 'Laman Utama',
            'â» æ¶è²»ç¨10%ãå«ã¿ã¾ã / Includes 10% Japanese Consumption Tax'       => 'Sudah termasuk Cukai Penggunaan Jepun 10%',
            'ð å¨å½ééå¯¾å¿ï¼ã¤ããéè¼¸ã»ä½å·æ¥ä¾¿ï¼ | Nationwide delivery via Yamato & Sagawa' => 'ð Penghantaran ke seluruh Jepun (Yamato / Sagawa)',
            'Added to cart!'                                                      => 'Ditambahkan ke troli!',
            'Out of Stock'                                                        => 'Kehabisan Stok',
            'Thank you for subscribing!'                                          => 'Terima kasih kerana melanggan!',
        ],

    ]; // end return [ 'en'=>..., 'id'=>..., 'ar'=>..., 'ms'=>... ]
}

// ââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
// 14. FALLBACK LANGUAGE DETECTION (cookie / query-string)
//     Used by halal_lang() when no multilingual plugin is active.
// ââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ

/**
 * Returns a 2-char language slug from cookie, query-string, or Accept-Language.
 * Sets the cookie when switching via ?lang=XX so future page-loads remember it.
 */
function halal_shop_get_fallback_lang(): string {
    static $cached = null;
    if ( $cached !== null ) return $cached;

    $allowed = array_keys( HALAL_LANGS ); // ['ja','en','id','ar','ms']

    // 1. Query string: ?lang=en  â highest priority, also sets cookie
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
           return $cached;
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
