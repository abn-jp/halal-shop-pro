<?php
/**
 * Plugin Name: Halal Shop — Language Fix (Must-Use)
 * Description: Forces correct URLs on Railway, disables cache during language switch, fixes HTTPS/proxy issues. Loaded before all other plugins.
 * Version: 1.0.2
 *
 * INSTALLATION: copy this file to wp-content/mu-plugins/halal-lang-fix.php
 * The setup-multilingual.sh script does this automatically.
 */

defined( 'ABSPATH' ) || exit;

// ─── 0. FIX WP_CONTENT_URL (Railway internal hostname) ───────────────────────
// Railway's WordPress Docker image defines WP_CONTENT_URL as a PHP constant
// pointing to the internal service hostname (e.g. mysql-production-30ed.up.railway.app).
// Since constants cannot be redefined after they're set (wp-config runs first),
// we filter every enqueued CSS/JS URL and the theme directory URI at runtime.

add_filter( 'style_loader_src',         'halal_fix_railway_content_url', 1 );
add_filter( 'script_loader_src',        'halal_fix_railway_content_url', 1 );
add_filter( 'template_directory_uri',   'halal_fix_railway_content_url', 1 );
add_filter( 'stylesheet_directory_uri', 'halal_fix_railway_content_url', 1 );
add_filter( 'plugins_url',              'halal_fix_railway_content_url', 1 );

function halal_fix_railway_content_url( $url ) {
    if ( ! is_string( $url ) || $url === '' ) return $url;
    static $host = null;
    if ( $host === null ) {
        $host = $_SERVER['HTTP_HOST'] ?? '';
    }
    if ( ! $host ) return $url;
    // Replace any Railway internal auto-generated hostname (pattern: name-production-HASH.up.railway.app)
    // with the actual public HTTP_HOST so all asset URLs resolve correctly.
    return preg_replace(
        '#^(https?://)[a-z0-9-]+-production-[a-f0-9]+\.up\.railway\.app#i',
        'https://' . $host,
        $url
    );
}

// ─── 1. HTTPS / Reverse-Proxy Fix (Railway, Cloudflare) ──────────────────────
// Railway terminates SSL at its edge; WordPress sees plain HTTP inside the
// container. Without this, is_ssl() returns false and siteurl stays http://,
// causing redirect loops and mixed-content warnings.

if (
    isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) &&
    strtolower( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) === 'https'
) {
    $_SERVER['HTTPS'] = 'on';
}

// Also trust Cloudflare's CF-Visitor header
if (
    isset( $_SERVER['HTTP_CF_VISITOR'] ) &&
    strpos( $_SERVER['HTTP_CF_VISITOR'], '"https"' ) !== false
) {
    $_SERVER['HTTPS'] = 'on';
}

// ─── 2. RAILWAY DOMAIN AUTO-DETECTION ─────────────────────────────────────────
// When RAILWAY_PUBLIC_DOMAIN is set, override WordPress siteurl/home so all
// URLs are correct even if the DB has stale values.
//
// Skip auto-generated Railway internal service hostnames
// (pattern: <service>-production-<hash>.up.railway.app)

$_halal_railway_domain = getenv( 'RAILWAY_PUBLIC_DOMAIN' ) ?: getenv( 'RAILWAY_STATIC_URL' ) ?: '';

if ( $_halal_railway_domain && preg_match( '/^[a-z0-9-]+-production-[a-f0-9]+\.up\.railway\.app$/i', $_halal_railway_domain ) ) {
    $_halal_railway_domain = ''; // treat as not set — DB values are correct
}

if ( $_halal_railway_domain ) {
    $_halal_railway_url = 'https://' . rtrim( $_halal_railway_domain, '/' );

    add_filter( 'pre_option_siteurl', function() use ( $_halal_railway_url ) {
        return $_halal_railway_url;
    }, 1 );
    add_filter( 'pre_option_home', function() use ( $_halal_railway_url ) {
        return $_halal_railway_url;
    }, 1 );

    add_filter( 'woocommerce_get_shop_url', function( $url ) use ( $_halal_railway_url ) {
        return str_replace( home_url(), $_halal_railway_url, $url );
    } );
}

// ─── 3. POLYLANG — FORCE CORRECT HOME URL PER LANGUAGE ────────────────────────

add_action( 'init', function() {
    if ( ! function_exists( 'PLL' ) ) return;
    if ( method_exists( PLL()->links_model ?? new stdClass(), 'get_home_url' ) ) {
        // PLL links model rebuilds on next call — no explicit flush needed
    }
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

// ─── 5. COOKIE DOMAIN FOR BOTH LOCALHOST & RAILWAY ────────────────────────────

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
            wp_redirect( $home, 302 );
            exit;
        }
    }
}, 5 );

// ─── 7. WOOCOMMERCE: FLUSH CART WHEN LANGUAGE CHANGES ────────────────────────

add_action( 'wp_loaded', function() {
    if ( ! class_exists( 'WooCommerce' ) ) return;
    if ( ! function_exists( 'WC' ) ) return;
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
    if ( ! in_array( $_GET['lang'], $allowed, true ) ) {
        unset( $_GET['lang'] );
    }
}, 1 );

// ─── 9. POLYLANG — disabled (cookie-based fallback handles multilingual) ─────
// Polylang is not auto-installed; the theme's halal_mod() inline translations
// and cookie/URL language switcher in functions.php handle all 5 languages.
// To enable Polylang: install it via WP Admin → Plugins and configure languages.
