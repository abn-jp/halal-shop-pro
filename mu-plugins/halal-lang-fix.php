<?php
/**
 * Plugin Name: Halal Shop — Language Fix (Must-Use)
 * Description: Forces correct URLs on Railway, disables cache during language switch, fixes HTTPS/proxy issues. Loaded before all other plugins.
 * Version: 1.0.3
 *
 * INSTALLATION: copy this file to wp-content/mu-plugins/halal-lang-fix.php
 * The setup-multilingual.sh script does this automatically.
 */

defined( 'ABSPATH' ) || exit;

// ─── 0. FIX WP_CONTENT_URL (Railway internal hostname) ───────────────────────

add_filter( 'style_loader_src',         'halal_fix_railway_content_url', 1 );
add_filter( 'script_loader_src',        'halal_fix_railway_content_url', 1 );
add_filter( 'template_directory_uri',   'halal_fix_railway_content_url', 1 );
add_filter( 'stylesheet_directory_uri', 'halal_fix_railway_content_url', 1 );
add_filter( 'plugins_url',              'halal_fix_railway_content_url', 1 );
add_filter( 'wp_get_attachment_url',    'halal_fix_railway_content_url', 1 );
add_filter( 'wp_get_attachment_image_src', function( $image ) {
    if ( is_array( $image ) && isset( $image[0] ) ) {
        $image[0] = halal_fix_railway_content_url( $image[0] );
    }
    return $image;
}, 1 );
add_filter( 'wp_calculate_image_srcset', function( $sources ) {
    if ( is_array( $sources ) ) {
        foreach ( $sources as &$source ) {
            if ( isset( $source['url'] ) ) {
                $source['url'] = halal_fix_railway_content_url( $source['url'] );
            }
        }
    }
    return $sources;
}, 1 );
add_filter( 'upload_dir', function( $uploads ) {
    if ( isset( $uploads['url'] ) )     $uploads['url']     = halal_fix_railway_content_url( $uploads['url'] );
    if ( isset( $uploads['baseurl'] ) ) $uploads['baseurl'] = halal_fix_railway_content_url( $uploads['baseurl'] );
    return $uploads;
}, 1 );

function halal_fix_railway_content_url( $url ) {
    if ( ! is_string( $url ) || $url === '' ) return $url;
    static $host = null;
    if ( $host === null ) {
        $host = $_SERVER['HTTP_HOST'] ?? '';
    }
    if ( ! $host ) return $url;
    return preg_replace(
        '#^(https?://)[a-z0-9-]+-production-[a-f0-9]+\.up\.railway\.app#i',
        'https://' . $host,
        $url
    );
}

// ─── 1. HTTPS / Reverse-Proxy Fix ────────────────────────────────────────────

if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && strtolower( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) === 'https' ) {
    $_SERVER['HTTPS'] = 'on';
}
if ( isset( $_SERVER['HTTP_CF_VISITOR'] ) && strpos( $_SERVER['HTTP_CF_VISITOR'], '"https"' ) !== false ) {
    $_SERVER['HTTPS'] = 'on';
}

// ─── 2. RAILWAY DOMAIN AUTO-DETECTION ────────────────────────────────────────

$_halal_railway_domain = getenv( 'RAILWAY_PUBLIC_DOMAIN' ) ?: getenv( 'RAILWAY_STATIC_URL' ) ?: '';
if ( $_halal_railway_domain && preg_match( '/^[a-z0-9-]+-production-[a-f0-9]+\.up\.railway\.app$/i', $_halal_railway_domain ) ) {
    $_halal_railway_domain = '';
}
if ( $_halal_railway_domain ) {
    $_halal_railway_url = 'https://' . rtrim( $_halal_railway_domain, '/' );
    add_filter( 'pre_option_siteurl', function() use ( $_halal_railway_url ) { return $_halal_railway_url; }, 1 );
    add_filter( 'pre_option_home',    function() use ( $_halal_railway_url ) { return $_halal_railway_url; }, 1 );
    add_filter( 'woocommerce_get_shop_url', function( $url ) use ( $_halal_railway_url ) {
        return str_replace( home_url(), $_halal_railway_url, $url );
    } );
}

// ─── 3. POLYLANG ──────────────────────────────────────────────────────────────

add_action( 'init', function() {
    if ( ! function_exists( 'PLL' ) ) return;
    if ( method_exists( PLL()->links_model ?? new stdClass(), 'get_home_url' ) ) {}
}, 1 );

// ─── 4. DISABLE PAGE CACHE DURING LANGUAGE SWITCH ────────────────────────────

$_halal_has_lang_signal =
    ( isset( $_GET['lang'] ) && $_GET['lang'] ) ||
    ( isset( $_COOKIE['halal_lang'] ) && $_COOKIE['halal_lang'] ) ||
    ( isset( $_COOKIE['pll_language'] ) && $_COOKIE['pll_language'] ) ||
    ( isset( $_COOKIE['wpml_browser_redirect_test'] ) );

if ( $_halal_has_lang_signal ) {
    if ( ! defined( 'DONOTCACHEPAGE' ) )   define( 'DONOTCACHEPAGE',   true );
    if ( ! defined( 'DONOTCACHEDB' ) )     define( 'DONOTCACHEDB',     true );
    if ( ! defined( 'DONOTMINIFY' ) )      define( 'DONOTMINIFY',      true );
    if ( ! defined( 'DONOTCACHEOBJECT' ) ) define( 'DONOTCACHEOBJECT', true );
    add_filter( 'rocket_cache_dynamic_cookies', function( $cookies ) {
        $cookies[] = 'halal_lang';
        $cookies[] = 'pll_language';
        return $cookies;
    } );
}

// ─── 5. COOKIE DOMAIN ────────────────────────────────────────────────────────

if ( ! defined( 'COOKIE_DOMAIN' ) ) {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $cookie_domain = preg_replace( '/:\d+$/', '', $host );
    define( 'COOKIE_DOMAIN', $cookie_domain === 'localhost' ? '' : $cookie_domain );
}

// ─── 6. POLYLANG — PREVENT 404 ON LANGUAGE PREFIX ────────────────────────────

add_action( 'template_redirect', function() {
    if ( ! function_exists( 'pll_current_language' ) ) return;
    if ( ! is_404() ) return;
    $lang = pll_current_language( 'slug' );
    if ( $lang && function_exists( 'pll_home_url' ) ) {
        $home = pll_home_url( $lang );
        if ( $home && $home !== ( ( is_ssl() ? 'https' : 'http' ) . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ) ) {
            wp_redirect( $home, 302 ); exit;
        }
    }
}, 5 );

// ─── 7. WOOCOMMERCE: FLUSH CART ON LANGUAGE CHANGE ───────────────────────────

add_action( 'wp_loaded', function() {
    if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'WC' ) ) return;
    $new_lang  = sanitize_key( $_GET['lang'] ?? '' );
    $prev_lang = sanitize_key( $_COOKIE['halal_lang'] ?? '' );
    if ( $new_lang && $prev_lang && $new_lang !== $prev_lang ) {
        if ( isset( WC()->session ) && WC()->session ) {
            WC()->session->set( 'wc_fragment_refresh', time() );
        }
    }
} );

// ─── 8. SECURITY: VALIDATE lang QUERY PARAMETER ──────────────────────────────

add_action( 'init', function() {
    if ( ! isset( $_GET['lang'] ) ) return;
    $allowed = [ 'ja', 'en', 'id', 'ar', 'ms' ];
    if ( ! in_array( $_GET['lang'], $allowed, true ) ) unset( $_GET['lang'] );
}, 1 );

// ─── 9. WOOCOMMERCE: FIX MISSING PLACEHOLDER IMAGE ───────────────────────────
// Docker containers don't persist wp-content/uploads between redeploys, so
// WooCommerce's uploads/woocommerce-placeholder.webp gets wiped. Fall back to
// the built-in placeholder inside the WooCommerce plugin directory instead.

add_filter( 'woocommerce_placeholder_img_src', function( $src ) {
    if ( strpos( $src, '/uploads/woocommerce-placeholder' ) !== false ) {
        if ( function_exists( 'WC' ) ) {
            return WC()->plugin_url() . '/assets/images/placeholder.png';
        }
    }
    return $src;
}, 10 );
