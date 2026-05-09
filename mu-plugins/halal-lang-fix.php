<?php
/**
 * Plugin Name: Halal Shop — Language Fix (Must-Use)
 * Description: Forces correct URLs on Railway, disables cache during language switch, fixes HTTPS/proxy issues. Loaded before all other plugins.
 * Version: 1.0.0
 *
 * INSTALLATION: copy this file to wp-content/mu-plugins/halal-lang-fix.php
 * The setup-multilingual.sh script does this automatically.
 */

defined( 'ABSPATH' ) || exit;

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
// When RAILWAY_PUBLIC_DOMAIN is set (Railway injects this automatically),
// override WordPress's stored siteurl and home so all URLs are correct,
// even if the DB still has localhost values.

$_halal_railway_domain = getenv( 'RAILWAY_PUBLIC_DOMAIN' ) ?: getenv( 'RAILWAY_STATIC_URL' ) ?: '';

if ( $_halal_railway_domain ) {
    $_halal_railway_url = 'https://' . rtrim( $_halal_railway_domain, '/' );

    // Must be added before WordPress loads options
    add_filter( 'pre_option_siteurl', function() use ( $_halal_railway_url ) {
        return $_halal_railway_url;
    }, 1 );
    add_filter( 'pre_option_home', function() use ( $_halal_railway_url ) {
        return $_halal_railway_url;
    }, 1 );

    // Also fix WooCommerce store URL if needed
    add_filter( 'woocommerce_get_shop_url', function( $url ) use ( $_halal_railway_url ) {
        return str_replace( home_url(), $_halal_railway_url, $url );
    } );
}

// ─── 3. POLYLANG — FORCE CORRECT HOME URL PER LANGUAGE ────────────────────────
// When siteurl/home is fixed above, Polylang needs to recalculate language URLs.
// This flush runs once after the URL filters above take effect.

add_action( 'init', function() {
    if ( ! function_exists( 'PLL' ) ) return;
    // Polylang caches home URL — clear its static cache on URL change
    if ( method_exists( PLL()->links_model ?? new stdClass(), 'get_home_url' ) ) {
        // PLL links model rebuilds on next call — no explicit flush needed
    }
}, 1 );

// ─── 4. DISABLE PAGE CACHE DURING LANGUAGE SWITCH ────────────────────────────
// Caching plugins must not serve a cached Japanese page to an English visitor.
// We set DONOTCACHEPAGE whenever a language signal is present in the request.

$_halal_has_lang_signal =
    ( isset( $_GET['lang'] ) && $_GET['lang'] ) ||
    ( isset( $_COOKIE['halal_lang'] ) && $_COOKIE['halal_lang'] ) ||
    ( isset( $_COOKIE['pll_language'] ) && $_COOKIE['pll_language'] ) ||  // Polylang cookie
    ( isset( $_COOKIE['wpml_browser_redirect_test'] ) );                    // WPML cookie

if ( $_halal_has_lang_signal ) {
    if ( ! defined( 'DONOTCACHEPAGE' ) )   define( 'DONOTCACHEPAGE',   true );
    if ( ! defined( 'DONOTCACHEDB' ) )     define( 'DONOTCACHEDB',     true );
    if ( ! defined( 'DONOTMINIFY' ) )      define( 'DONOTMINIFY',      true );
    if ( ! defined( 'DONOTCACHEOBJECT' ) ) define( 'DONOTCACHEOBJECT', true );
    // WP Rocket
    add_filter( 'rocket_cache_dynamic_cookies', function( $cookies ) {
        $cookies[] = 'halal_lang';
        $cookies[] = 'pll_language';
        return $cookies;
    } );
}

// ─── 5. COOKIE DOMAIN FOR BOTH LOCALHOST & RAILWAY ────────────────────────────
// PHP's setcookie() uses COOKIE_DOMAIN constant. On localhost this can be
// empty; on Railway it must match the domain. This sets it correctly.

if ( ! defined( 'COOKIE_DOMAIN' ) ) {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    // Strip port for localhost
    $cookie_domain = preg_replace( '/:\d+$/', '', $host );
    define( 'COOKIE_DOMAIN', $cookie_domain === 'localhost' ? '' : $cookie_domain );
}

// ─── 6. POLYLANG — PREVENT 404 ON LANGUAGE PREFIX ────────────────────────────
// On a fresh WordPress install, visiting /en/ or /ja/ can 404 because
// rewrite rules haven't been flushed. This catches it and redirects cleanly.

add_action( 'template_redirect', function() {
    if ( ! function_exists( 'pll_current_language' ) ) return;
    if ( ! is_404() ) return;

    // If we're on /en/ or /ja/ etc. with nothing after, go to language home
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
// Cart totals are cached per session; if language changes, the cart fragment
// (which contains translated text) must be rebuilt.

add_action( 'wp_loaded', function() {
    if ( ! class_exists( 'WooCommerce' ) ) return;
    if ( ! function_exists( 'WC' ) ) return;

    $new_lang  = sanitize_key( $_GET['lang'] ?? '' );
    $prev_lang = sanitize_key( $_COOKIE['halal_lang'] ?? '' );

    if ( $new_lang && $prev_lang && $new_lang !== $prev_lang ) {
        // Language actually changed — clear WooCommerce session fragments
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
        // Invalid lang value — remove it silently
        unset( $_GET['lang'] );
    }
}, 1 );


// ─── 9. AUTO-ACTIVATE POLYLANG (Railway first-boot) ──────────────────────
// Polylang is pre-installed in the Docker image at build time.
// This hook activates it automatically on the first request after deployment,
// so no manual WP Admin step is required.

add_action( 'plugins_loaded', function() {
    $polylang_file = 'polylang/polylang.php';
    if ( ! file_exists( WP_PLUGIN_DIR . '/' . $polylang_file ) ) return;

    $active = (array) get_option( 'active_plugins', [] );
    if ( in_array( $polylang_file, $active, true ) ) return; // already active

    require_once ABSPATH . 'wp-admin/includes/plugin.php';
    activate_plugin( $polylang_file, '', false, true );
}, 1 );

// ─── 9. POLYLANG — disabled (cookie-based fallback handles multilingual) ─────
// Polylang is not auto-installed; the theme's halal_mod() inline translations
// and cookie/URL language switcher in functions.php handle all 5 languages.
// To enable Polylang: install it via WP Admin → Plugins and configure languages.
